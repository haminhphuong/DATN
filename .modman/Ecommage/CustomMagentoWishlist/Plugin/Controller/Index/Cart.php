<?php
namespace Ecommage\CustomMagentoWishlist\Plugin\Controller\Index;

use Magento\Catalog\Helper\Product;
use Magento\Catalog\Model\Product\Exception as ProductException;
use Magento\Checkout\Model\Cart as CheckoutCart;
use Magento\Checkout\Helper\Cart as CartHelper;
use Magento\Framework\App\Action;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\Controller\Result\Json;
use Magento\Framework\Controller\Result\Redirect;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Data\Form\FormKey\Validator;
use Magento\Framework\Escaper;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Stdlib\Cookie\CookieMetadataFactory;
use Magento\Framework\Stdlib\Cookie\PublicCookieMetadata;
use Magento\Framework\Stdlib\CookieManagerInterface;
use Magento\Wishlist\Controller\WishlistProviderInterface;
use Magento\Wishlist\Helper\Data;
use Magento\Wishlist\Model\Item\OptionFactory;
use Magento\Wishlist\Model\ItemFactory;
use Magento\Wishlist\Model\LocaleQuantityProcessor;
use Magento\Wishlist\Model\ResourceModel\Item\Option\Collection;

class Cart
{
    /**
     * @var WishlistProviderInterface
     */
    protected $wishlistProvider;

    /**
     * @var LocaleQuantityProcessor
     */
    protected $quantityProcessor;

    /**
     * @var ItemFactory
     */
    protected $itemFactory;

    /**
     * @var CheckoutCart
     */
    protected $cart;

    /**
     * @var CartHelper
     */
    protected $cartHelper;

    /**
     * @var OptionFactory
     */
    protected $optionFactory;

    /**
     * @var Product
     */
    protected $productHelper;

    /**
     * @var Escaper
     */
    protected $escaper;

    /**
     * @var Data
     */
    protected $helper;

    /**
     * @var Validator
     */
    protected $formKeyValidator;

    /**
     * @var CookieManagerInterface
     */
    protected $cookieManager;

    /**
     * @var CookieMetadataFactory
     */
    protected $cookieMetadataFactory;
    /**
     * @var ResultFactory
     */
    protected $resultFactory;
    /**
     * @var \Magento\Framework\UrlInterface
     */
    protected $_url;
    /**
     * @var \Magento\Framework\Message\ManagerInterface
     */
    protected $messageManager;
    protected $_redirect;

    /**
     * @param Action\Context $context
     * @param WishlistProviderInterface $wishlistProvider
     * @param LocaleQuantityProcessor $quantityProcessor
     * @param ItemFactory $itemFactory
     * @param CheckoutCart $cart
     * @param OptionFactory $optionFactory
     * @param Product $productHelper
     * @param Escaper $escaper
     * @param Data $helper
     * @param CartHelper $cartHelper
     * @param Validator $formKeyValidator
     * @param CookieManagerInterface|null $cookieManager
     * @param CookieMetadataFactory|null $cookieMetadataFactory
     */
    public function __construct(
        Action\Context $context,
        WishlistProviderInterface $wishlistProvider,
        LocaleQuantityProcessor $quantityProcessor,
        ItemFactory $itemFactory,
        CheckoutCart $cart,
        OptionFactory $optionFactory,
        Product $productHelper,
        Escaper $escaper,
        Data $helper,
        CartHelper $cartHelper,
        Validator $formKeyValidator,
        ?CookieManagerInterface $cookieManager = null,
        ?CookieMetadataFactory $cookieMetadataFactory = null
    ) {
        $this->wishlistProvider = $wishlistProvider;
        $this->quantityProcessor = $quantityProcessor;
        $this->itemFactory = $itemFactory;
        $this->cart = $cart;
        $this->optionFactory = $optionFactory;
        $this->productHelper = $productHelper;
        $this->escaper = $escaper;
        $this->helper = $helper;
        $this->cartHelper = $cartHelper;
        $this->formKeyValidator = $formKeyValidator;
        $this->cookieManager = $cookieManager ?: ObjectManager::getInstance()->get(CookieManagerInterface::class);
        $this->cookieMetadataFactory = $cookieMetadataFactory ?: ObjectManager::getInstance()->get(CookieMetadataFactory::class);
        $this->resultFactory = $context->getResultFactory();
        $this->_url = $context->getUrl();
        $this->messageManager = $context->getMessageManager();
        $this->_redirect = $context->getRedirect();
    }

    /**
     * Add wishlist item to shopping cart and remove from wishlist
     *
     * If Product has required options - item removed from wishlist and redirect
     * to product view page with message about needed defined required options
     *
     * @return ResultInterface
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    public function aroundExecute(\Magento\Wishlist\Controller\Index\Cart $subject, callable $proceed)
    {
        /** @var Redirect $resultRedirect */
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        if (!$this->formKeyValidator->validate($subject->getRequest())) {
            return $resultRedirect->setPath('*/*/');
        }

        $itemId = (int)$subject->getRequest()->getParam('item');
        /* @var $item \Magento\Wishlist\Model\Item */
        $item = $this->itemFactory->create()->load($itemId);
        if (!$item->getId()) {
            $resultRedirect->setPath('*/*');
            return $resultRedirect;
        }
        $wishlist = $this->wishlistProvider->getWishlist($item->getWishlistId());
        if (!$wishlist) {
            $resultRedirect->setPath('*/*');
            return $resultRedirect;
        }

        // Set qty
        $qty = $subject->getRequest()->getParam('qty');
        $postQty = $subject->getRequest()->getPostValue('qty');
        if ($postQty !== null && $qty !== $postQty) {
            $qty = $postQty;
        }
        if (is_array($qty)) {
            if (isset($qty[$itemId])) {
                $qty = $qty[$itemId];
            } else {
                $qty = 1;
            }
        }
        $qty = $this->quantityProcessor->process($qty);
        if ($qty) {
            $item->setQty($qty);
        }

        $redirectUrl = $this->_url->getUrl('*/*');
        $configureUrl = $this->_url->getUrl(
            '*/*/configure/',
            [
                'id' => $item->getId(),
                'product_id' => $item->getProductId(),
            ]
        );
        try {
            /** @var Collection $options */
            $options = $this->optionFactory->create()->getCollection()->addItemFilter([$itemId]);
            $item->setOptions($options->getOptionsByItem($itemId));

            $buyRequest = $this->productHelper->addParamsToBuyRequest(
                $subject->getRequest()->getParams(),
                ['current_config' => $item->getBuyRequest()]
            );

            $item->mergeBuyRequest($buyRequest);
            $item->addToCart($this->cart, true);

            $related = $subject->getRequest()->getParam('related_product');
            if (!empty($related)) {
                $this->cart->addProductsByIds(explode(',', $related));
            }

            $this->cart->save()->getQuote()->collectTotals();
            $wishlist->save();

            if (!$this->cart->getQuote()->getHasError()) {
                $this->messageManager->addComplexSuccessMessage(
                    'addCartSuccessMessage',
                    [
                        'product_name' => $item->getProduct()->getName(),
                        'cart_url' => $this->cartHelper->getCartUrl()
                    ]
                );
                $productsToAdd = [
                    [
                        'sku' => $item->getProduct()->getSku(),
                        'name' => $item->getProduct()->getName(),
                        'price' => $item->getProduct()->getFinalPrice(),
                        'qty' => $item->getQty(),
                    ]
                ];

                /** @var PublicCookieMetadata $publicCookieMetadata */
                $publicCookieMetadata = $this->cookieMetadataFactory->createPublicCookieMetadata()
                    ->setDuration(3600)
                    ->setPath('/')
                    ->setHttpOnly(false)
                    ->setSameSite('Strict');

                $this->cookieManager->setPublicCookie(
                    'add_to_cart',
                    \rawurlencode(\json_encode($productsToAdd)),
                    $publicCookieMetadata
                );
            }

            if ($this->cartHelper->getShouldRedirectToCart()) {
                $redirectUrl = $this->cartHelper->getCartUrl();
            } else {
                $refererUrl = $this->_redirect->getRefererUrl();
                if ($refererUrl) {
                    $redirectUrl = $refererUrl;
                }
            }
        } catch (ProductException $e) {
            $this->messageManager->addErrorMessage(__('This product(s) is out of stock.'));
        } catch (LocalizedException $e) {
            if($item->getProduct()->getTypeId() == 'bundle'){
                $item->delete();
                $this->messageManager->addComplexSuccessMessage(
                    'addCartSuccessMessage',
                    [
                        'product_name' => $item->getProduct()->getName(),
                        'cart_url' => $this->cartHelper->getCartUrl()
                    ]
                );
            }else{
                $this->messageManager->addNoticeMessage($e->getMessage());
                $redirectUrl = $configureUrl;
            }

        } catch (\Exception $e) {
            $this->messageManager->addExceptionMessage($e, __('We can\'t add the item to the cart right now.'));
        }

        $this->helper->calculate();

        if ($subject->getRequest()->isAjax()) {
            /** @var Json $resultJson */
            $resultJson = $this->resultFactory->create(ResultFactory::TYPE_JSON);
            $resultJson->setData(['backUrl' => $redirectUrl]);
            return $resultJson;
        }

        $resultRedirect->setUrl($redirectUrl);
        return $resultRedirect;
    }
}
