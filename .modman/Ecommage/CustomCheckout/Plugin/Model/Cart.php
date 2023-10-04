<?php

namespace Ecommage\CustomCheckout\Plugin\Model;

use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Catalog\Model\Product;
use Magento\Checkout\Model\Session;
use Magento\Framework\DataObject;
use Magento\Framework\Event\ManagerInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Store\Model\StoreManagerInterface;
use Magento\CatalogInventory\Api\StockRegistryInterface;
use Magento\Framework\UrlInterface;
class Cart
{
    /**
     * @var ManagerInterface
     */
    protected $_eventManager;
    /**
     * @var Session
     */
    protected $_checkoutSession;
    /**
     * @var StoreManagerInterface
     */
    protected $_storeManager;
    /**
     * @var ProductRepositoryInterface
     */
    protected $productRepository;
    /**
     * @var StockRegistryInterface
     */
    protected $stockRegistry;
    /**
     * @var \Magento\Checkout\Model\Cart\RequestInfoFilterInterface
     */
    protected $requestInfoFilter;
    protected $url;
    /**
     * @param ManagerInterface $eventManager
     * @param Session $checkoutSession
     */
    public function __construct
    (
        ManagerInterface $eventManager,
        Session $checkoutSession,
        StoreManagerInterface $storeManager,
        ProductRepositoryInterface $productRepository,
        StockRegistryInterface $stockRegistry,
        UrlInterface $url
    )
    {
        $this->_eventManager = $eventManager;
        $this->_checkoutSession = $checkoutSession;
        $this->_storeManager = $storeManager;
        $this->productRepository = $productRepository;
        $this->stockRegistry = $stockRegistry;
        $this->url = $url;
    }

    /**
     * Add product to shopping cart (quote)
     *
     * @param int|Product $productInfo
     * @param \Magento\Framework\DataObject|int|array $requestInfo
     * @return \Magento\Checkout\Model\Cart
     * @throws \Magento\Framework\Exception\LocalizedException
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    public function aroundAddProduct(\Magento\Checkout\Model\Cart $subject, callable $proceed, $productInfo, $requestInfo = null)
    {
        $product = $this->_getProduct($productInfo);
        $productId = $product->getId();

        if ($productId) {
            $request = $this->getQtyRequest($product, $requestInfo);
            try {
                $this->_eventManager->dispatch(
                    'checkout_cart_product_add_before',
                    ['info' => $requestInfo, 'product' => $product]
                );
                $result = $subject->getQuote()->addProduct($product, $request);
            } catch (\Magento\Framework\Exception\LocalizedException $e) {
                $this->_checkoutSession->setUseNotice(false);
                $result = $e->getMessage();
            }
            /**
             * String we can get if prepare process has error
             */
            if (is_string($result)) {
                if ($product->hasOptionsValidationFail()) {
                    if($product->getTypeId() == 'bundle'){
                        $qty = 1;
                        $productsArray = $this->getBundleOptions($product);
                        $params = [
                            'product' => $productId,
                            'bundle_option' => $productsArray,
                            'qty' => $qty
                        ];
                        $subject->addProduct($product, $params);
                        if(strpos($this->url->getCurrentUrl(), 'checkout/cart/add')){
                            $this->_checkoutSession->setLastAddedProductId($productId);
                            return $this;
                        }
                        if(!strpos($this->url->getCurrentUrl(), 'wishlist/index/allcart')){
                            $subject->save();
                        }
                        $this->_checkoutSession->setRedirectUrl($this->url->getBaseUrl().'checkout');
                    }else{
                        $redirectUrl = $product->getUrlModel()->getUrl(
                            $product,
                            ['_query' => ['startcustomization' => 1]]
                        );
                        $this->_checkoutSession->setRedirectUrl($redirectUrl);
                    }
                } else {
                    $redirectUrl = $product->getProductUrl();
                    $this->_checkoutSession->setRedirectUrl($redirectUrl);
                }
                if ($this->_checkoutSession->getUseNotice() === null) {
                    $this->_checkoutSession->setUseNotice(true);
                }
                throw new \Magento\Framework\Exception\LocalizedException(__($result));
            }
        } else {
            throw new \Magento\Framework\Exception\LocalizedException(__('The product does not exist.'));
        }

        $this->_eventManager->dispatch(
            'checkout_cart_product_add_after',
            ['quote_item' => $result, 'product' => $product]
        );
        $this->_checkoutSession->setLastAddedProductId($productId);
        return $this;
    }

    /**
     * get all the selection products used in bundle product
     * @param $product
     * @return mixed
     */
    public function getBundleOptions(Product $product)
    {
        $selectionCollection = $product->getTypeInstance()
            ->getSelectionsCollection(
                $product->getTypeInstance()->getOptionsIds($product),
                $product
            );
        $bundleOptions = [];
        foreach ($selectionCollection as $selection) {
            $bundleOptions[$selection->getOptionId()][] = $selection->getSelectionId();
        }
        return $bundleOptions;
    }

    /**
     * Get product object based on requested product information
     *
     * @param Product|int|string $productInfo
     * @return Product
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function _getProduct($productInfo)
    {
        $product = null;
        if ($productInfo instanceof Product) {
            $product = $productInfo;
            if (!$product->getId()) {
                throw new \Magento\Framework\Exception\LocalizedException(
                    __("The product wasn't found. Verify the product and try again.")
                );
            }
        } elseif (is_int($productInfo) || is_string($productInfo)) {
            $storeId = $this->_storeManager->getStore()->getId();
            try {
                $product = $this->productRepository->getById($productInfo, false, $storeId);
            } catch (NoSuchEntityException $e) {
                throw new \Magento\Framework\Exception\LocalizedException(
                    __("The product wasn't found. Verify the product and try again."),
                    $e
                );
            }
        } else {
            throw new \Magento\Framework\Exception\LocalizedException(
                __("The product wasn't found. Verify the product and try again.")
            );
        }
        $currentWebsiteId = $this->_storeManager->getStore()->getWebsiteId();
        if (!is_array($product->getWebsiteIds()) || !in_array($currentWebsiteId, $product->getWebsiteIds())) {
            throw new \Magento\Framework\Exception\LocalizedException(
                __("The product wasn't found. Verify the product and try again.")
            );
        }
        return $product;
    }

    /**
     * Get request quantity
     *
     * @param Product $product
     * @param \Magento\Framework\DataObject|int|array $request
     * @return int|DataObject
     */
    public function getQtyRequest($product, $request = 0)
    {
        $request = $this->_getProductRequest($request);
        $stockItem = $this->stockRegistry->getStockItem($product->getId(), $product->getStore()->getWebsiteId());
        $minimumQty = $stockItem->getMinSaleQty();
        //If product quantity is not specified in request and there is set minimal qty for it
        if ($minimumQty
            && $minimumQty > 0
            && !$request->getQty()
        ) {
            $request->setQty($minimumQty);
        }

        return $request;
    }

    /**
     * Get request for product add to cart procedure
     *
     * @param \Magento\Framework\DataObject|int|array $requestInfo
     * @return \Magento\Framework\DataObject
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function _getProductRequest($requestInfo)
    {
        if ($requestInfo instanceof \Magento\Framework\DataObject) {
            $request = $requestInfo;
        } elseif (is_numeric($requestInfo)) {
            $request = new \Magento\Framework\DataObject(['qty' => $requestInfo]);
        } elseif (is_array($requestInfo)) {
            $request = new \Magento\Framework\DataObject($requestInfo);
        } else {
            throw new \Magento\Framework\Exception\LocalizedException(
                __('We found an invalid request for adding product to quote.')
            );
        }
        $this->getRequestInfoFilter()->filter($request);

        return $request;
    }

    /**
     * Getter for RequestInfoFilter
     *
     * @deprecated 100.1.2
     * @return \Magento\Checkout\Model\Cart\RequestInfoFilterInterface
     */
    public function getRequestInfoFilter()
    {
        if ($this->requestInfoFilter === null) {
            $this->requestInfoFilter = \Magento\Framework\App\ObjectManager::getInstance()
                ->get(\Magento\Checkout\Model\Cart\RequestInfoFilterInterface::class);
        }
        return $this->requestInfoFilter;
    }
}
