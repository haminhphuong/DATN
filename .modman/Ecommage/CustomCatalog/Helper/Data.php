<?php

namespace Ecommage\CustomCatalog\Helper;

use Magento\Customer\Model\SessionFactory as CustomerSession;
use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\Registry;
use Magento\Store\Model\ScopeInterface;
use Magento\Wishlist\Model\ItemFactory;
use Magento\Catalog\Api\CategoryRepositoryInterface;
use Magento\Catalog\Model\CategoryFactory;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Widget\Helper\Conditions;
use Magento\Catalog\Model\Category;
use Magento\Catalog\Model\Product;
use Magento\Catalog\Model\ProductRepository;
use Magento\Framework\UrlInterface;
use Magento\InventorySalesAdminUi\Model\GetSalableQuantityDataBySku;
use Magento\Eav\Model\Entity\Attribute;

/**
 * Class Data
 *
 * @package Ecommage\CustomCatalog\Helper
 */
class Data extends AbstractHelper
{
    const COMPARE_PATH      = 'compare/';
    const BUYNOW_PATH       = 'buynow/';
    const AUTO_APPROVE_REVIEWS_PATH       = 'auto_approve_reviews/';
    const ADDTOCART_FORM_ID = 'ADDTOCART_FORM_ID';
    const PER_ROW = 'per_row';
    const TWO_PER_ROW = 2;
    const FOUR_PER_ROW = 4;
    const SIZE_GUIDE_BLOCK = 'web/blocks/size_guide';
    const PERCENTAGE_ENABLED='percentage/';
    /**
     * Core registry
     *
     * @var Registry
     */
    protected $_coreRegistry;
    /**
     * @var ItemFactory
     */
    protected $_wishlistItemFactory;
    /**
     * @var Conditions
     */
    protected $conditions;
    /**
     * @var CategoryRepositoryInterface
     */
    protected $categoryRepository;
    /**
     * @var StoreManagerInterface
     */
    protected $storeManager;
    /**
     * @var CategoryFactory
     */
    protected $categoryFactory;
    /**
     * @var CustomerSession
     */
    protected $customerSession;
    /**
     * @var Category
     */
    protected $_category;
    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManagerInterface;
    /**
     * @var Product
     */
    protected $_product;
    /**
     * @var ProductRepository
     */
    protected $_productRepository;
    /**
     * @var UrlInterface
     */
    protected $url;
    /**
     * @var \Magento\Framework\App\RequestInterface
     */
    protected $request;

    /**
     * @var GetSalableQuantityDataBySku
     */
    protected $getSalableQuantityDataBySku;
    /**
     * @var Attribute
     */
    protected $attribute;

    /**
     * @param Conditions $conditions
     * @param CategoryRepositoryInterface $categoryRepository
     * @param StoreManagerInterface $storeManager
     * @param CategoryFactory $categoryFactory
     * @param Context $context
     * @param Registry $_coreRegistry
     * @param ItemFactory $_wishlistItemFactory
     * @param CustomerSession $customerSession
     * @param Category $category
     * @param StoreManagerInterface $storeManagerInterface
     * @param Product $product
     */
    public function __construct(
        Conditions $conditions,
        CategoryRepositoryInterface $categoryRepository,
        StoreManagerInterface $storeManager,
        CategoryFactory $categoryFactory,
        Context $context,
        Registry $_coreRegistry,
        ItemFactory $_wishlistItemFactory,
        CustomerSession $customerSession,
        Category $category,
        StoreManagerInterface $storeManagerInterface,
        Product $product,
        ProductRepository $productRepository,
        UrlInterface $url,
        GetSalableQuantityDataBySku $getSalableQuantityDataBySku,
        Attribute $attribute
    ) {
        $this->conditions           = $conditions;
        $this->categoryRepository   = $categoryRepository;
        $this->storeManager         = $storeManager;
        $this->categoryFactory      = $categoryFactory;
        $this->_coreRegistry        = $_coreRegistry;
        $this->_wishlistItemFactory = $_wishlistItemFactory;
        $this->customerSession      = $customerSession;
        $this->_category = $category;
        $this->_storeManagerInterface = $storeManagerInterface;
        $this->_product = $product;
        $this->_productRepository = $productRepository;
        $this->url = $url;
        $this->request = $context->getRequest();
        $this->getSalableQuantityDataBySku = $getSalableQuantityDataBySku;
        $this->attribute = $attribute;
        parent::__construct($context);
    }

    /**
     * @return string
     */
    public function getLimitCompared()
    {
        return $this->getCompareConfig('general/limit');
    }

    /**
     * @param      $path
     * @param null $storeId
     *
     * @return mixed
     */
    public function getCompareConfig($path, $storeId = null)
    {
        return $this->scopeConfig->getValue(
            self::COMPARE_PATH . $path,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    /**
     * @return mixed
     */
    public function getAddToCartFormId()
    {
        $addToCartFormId = $this->getBuyNowConfig('general/addtocart_id');
        return $addToCartFormId ? $addToCartFormId : self::ADDTOCART_FORM_ID;
    }

    /**
     * @param      $path
     * @param null $storeId
     *
     * @return mixed
     */
    public function getBuyNowConfig($path, $storeId = null)
    {
        return $this->scopeConfig->getValue(
            self::BUYNOW_PATH . $path,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    /**
     * @param $path
     * @param null $storeId
     * @return mixed
     */
    public function getAutoApproveReviewConfig($path, $storeId = null)
    {
        return $this->scopeConfig->getValue(
            self::AUTO_APPROVE_REVIEWS_PATH . $path,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    /**
     * @return mixed
     */
    public function keepCartProducts()
    {
        return $this->getBuyNowConfig('general/keep_cart_products');
    }

    public function getConditions($conditions)
    {
        if ($conditions) {
            $conditions = $this->conditions->decode($conditions);
        }
        $categoryCondition = [];
        foreach ($conditions as $condition) {
            if (!empty($condition['attribute'])) {
                if ($condition['attribute'] == 'category_ids') {
                    $categoryCondition = $this->updateAnchorCategoryConditions($condition);
                    if (isset($categoryCondition['operator'])) {
                        if ($categoryCondition['operator'] == "==") {
                            return $categoryCondition['value'];
                        }
                    }

                }
            }
        }
        return null;
    }

    private function updateAnchorCategoryConditions($condition)
    {
        if (array_key_exists('value', $condition)) {
            $categoryId = $condition['value'];

            try {
                $category = $this->categoryRepository->get($categoryId, $this->storeManager->getStore()->getId());
            } catch (NoSuchEntityException $e) {
                return $condition;
            }

            $children = $category->getIsAnchor() ? $category->getChildren(true) : [];

            if ($children) {
                $children              = explode(',', $children);
                $condition['operator'] = "()";
                $condition['value']    = array_merge([$categoryId], $children);
            }
        }

        return $condition;
    }

    public function getCategory($categoryId)
    {
        try {
            $category = $this->categoryFactory->create()->load($categoryId);
            return $category;
        } catch (NoSuchEntityException $e) {
            return null;
        }
        return null;
    }

    public function getBaseUrl()
    {
        return $this->storeManager->getStore()->getBaseUrl();
    }

    public function getCategoryUrl($categoryId)
    {
        $category = $this->categoryRepository->get($categoryId, $this->storeManager->getStore()->getId());
        if ($category->getId()) {
            return $category->getUrl();
        }
        return '';
    }

    /**
     * @return mixed
     */
    public function productIsWishlist()
    {
        $count             = 0;
        $customerIdCurrent = $this->getCurrentCustomer()->getId();
        $productIdCurrent  = $this->getCurrentProduct()->getId();
        if ($customerIdCurrent) {
            $wishlistCollection = $this->_wishlistItemFactory->create()->getCollection()
                                                             ->addFieldToFilter('product_id', $productIdCurrent)
                                                             ->addCustomerIdFilter($customerIdCurrent);
            $count              = $wishlistCollection->getSize();

        }
        return $count;
    }

    /**
     * @return mixed|null
     */
    public function getCurrentProduct()
    {
        return $this->_coreRegistry->registry('current_product');
    }

    /**
     * @return mixed
     */
    public function getCurrentCustomer()
    {
        return $this->customerSession->create()->getCustomer();
    }

    /**
     * @param $currentUrl
     *
     * @return mixed
     * @SuppressWarnings(PHPMD.ElseExpression)
     */
    public function builUrl($currentUrl, $value)
    {
        $url = $currentUrl;
        if (strpos($currentUrl, self::PER_ROW) === false){
            if (strpos($currentUrl, '?') === false) {
                $url = $currentUrl.'?'.self::PER_ROW.'='.$value;
            }else{
                $url = $currentUrl.'&'.self::PER_ROW.'='.$value;
            }
        }else{
            if ($value == self::TWO_PER_ROW){
                $url = str_replace("per_row=". self::FOUR_PER_ROW, "per_row=". self::TWO_PER_ROW, $currentUrl);
            }else{
                $url = str_replace("per_row=". self::TWO_PER_ROW, "per_row=". self::FOUR_PER_ROW, $currentUrl);
            }
        }

        return $url;
    }

    /**
     * @param      $path
     * @param null $storeId
     *
     * @return string
     */
    public function getSizeGuide($storeId = null)
    {
        return $this->scopeConfig->getValue(
            self::SIZE_GUIDE_BLOCK,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    /**
     * @return int
     * @throws NoSuchEntityException
     */
    public function getStoreId(){
        return $this->storeManager->getStore()->getId();
    }

    /**
     * @return string
     * @throws NoSuchEntityException
     */
    public function getRandomUrlCategory(){
        $store  = $this->_storeManagerInterface->getStore()->getId();
        $rootCategoryId = $this->_storeManagerInterface->getStore($store)->getRootCategoryId();
        if($this->getCurrentProduct()){
            $categories = $this->getCurrentProduct()->getCategoryIds();
            if (($key = array_search($rootCategoryId, $categories)) !== false) {
                unset($categories[$key]);
            }
            if(!$categories){
                return false;
            }
            $randCategory = array_rand($categories,1);

            $category = $this->_category->load($categories[$randCategory]);
            return $category->getUrl();
        }
        return '#';


    }

    /**
     * @return Product
     */
    public function getModelProduct(){
        return $this->_product;
    }

    /**
     * @param $id
     *
     * @return \Magento\Catalog\Api\Data\ProductInterface|mixed|null
     * @throws NoSuchEntityException
     */
    public function getProduct($id)
    {
        return $this->_productRepository->getById($id);
    }

    /**
     * @param $product
     * @return bool
     */
    public function checkQtyAndStockOptionProduct($product){
        if($product->getTypeId() == "bundle"){
            $selectionCollection = $product->getTypeInstance()
                ->getSelectionsCollection(
                    $product->getTypeInstance()->getOptionsIds($product),
                    $product
                );
            foreach ($selectionCollection as $selection) {
                $productRepository = $this->_productRepository->get($selection['sku']);
                $salable = $this->getSalableQuantityDataBySku->execute($selection['sku']);

                $qtyAndStock = $productRepository->getQuantityAndStockStatus();

                if($qtyAndStock){
                    if($qtyAndStock['is_in_stock']){
                        if($salable[0]['qty'] <= 0){
                            return false;
                        }
                    }else{
                        return false;
                    }
                }
            }

        }
        if($product->getTypeId() == "simple"){
            $salable = $this->getSalableQuantityDataBySku->execute($product->getSku());
            $productRepository = $this->_productRepository->get($product->getSku());
            $qtyAndStock = $productRepository->getQuantityAndStockStatus();

            if(isset($qtyAndStock['is_in_stock']) && $qtyAndStock['is_in_stock']){
                if($product->getPrice() == 0){
                    return false;
                }
                if($salable[0]['qty'] <= 0 && $product->getPrice() == 0){
                    return false;
                }
            }else{
                return false;
            }
        }

        return true;
    }

    /**
     * @param $limit
     * @return string
     * @SuppressWarnings(PHPMD.ElseExpression)
     */
    public function updateUrlPrice($value){
        $currentUrl  = $this->url->getCurrentUrl();
        $valuePrice = $this->request->getParam('price');
        $strPrice = 'price='.$valuePrice;

        if(!$valuePrice){
            if(strpos($currentUrl,'?')){
                $currentUrl .= '&'.$strPrice.strtolower($value);
            }else{
                $currentUrl .= '?'.$strPrice.strtolower($value);
            }
        }
        $newStrPrice = str_replace($valuePrice,$value,$strPrice);

        if($valuePrice && !strpos($valuePrice,'price=')){
            $newStrPrice = str_replace('price=','',$newStrPrice);
        }
        $currentUrl = str_replace($valuePrice,$newStrPrice,$currentUrl);

        return $currentUrl;
    }

    /**
     * @param $price
     * @return bool
     */
    public function checkedFilterPrice($price){
        $valuePriceUrl = $this->request->getParam('price');
        if($valuePriceUrl && $valuePriceUrl == $price){
            return true;
        }
        return false;
    }

    /**
     * @return false|int|string
     */
    public function getLabelFilerPrice(){
        $valuePriceUrl = $this->request->getParam('price');
        $arrPrices = $this->getArrayPrices();
        if($valuePriceUrl){
            foreach ($arrPrices as $key=>$price){
                if($valuePriceUrl == $price){
                    return $key;
                }
            }
            if($valuePriceUrl == '100000000.00-3000000000.00'){
                return 'From 100 million to 3 billion';
            }
        }

        return false;
    }

    /**
     * @return string[]
     */
    public function getArrayPrices(){
        return ['Under 2 million'=>'0.00-1999999.00',
                'From 2 million to 5 million'=>'2000000.00-5000000.00',
                'From 5 million to 10 million'=>'5000000.00-10000000.00',
                'From 10 million to 20 million'=>'10000000.00-20000000.00',
                'From 20 million to 50 million'=>'20000000.00-50000000.00',
                'From 50 million to 100 million'=>'50000000.00-100000000.00',
                'From 100 million to 300 million'=>'100000000.00-300000000.00',
                'Over 300 million'=>'300000000.00-300000000000.00'];
    }

    /**
     * Load attribute data by code
     * @return  \Magento\Eav\Model\Entity\Attribute
     */
    public function getAttributeInfo($attributeCode)
    {
        return $this->attribute->loadByCode('catalog_product', $attributeCode);
    }
    /**
     * @param $currentPrice
     * @param $oldPrice
     * @returns int
     */
    public function getPercentDiscountPrice($currentPrice,$oldPrice){
        $percent=($oldPrice-$currentPrice)/$oldPrice*100;

        return round($percent);
    }
    /**
     * @param      $path
     * @param null $storeId
     *
     * @return mixed
     */
    public function getPercentTageConfig($path, $storeId = null)
    {
        return $this->scopeConfig->getValue(
            self::PERCENTAGE_ENABLED . $path,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    /**
     * @return mixed
     */
    public function isPercentWithSample()
    {
        return $this->getPercentTageConfig('general/enable_with_sample');
    }
    /**
     * @return mixed
     */
    public function isPercentConfigurationProduct()
    {
        return $this->getPercentTageConfig('general/enable_with_configuration');
    }

    /**
     * @param $html
     * @param int $limit
     * @param null $endSubstitute
     * @param bool $lineBreak
     * @return string|string[]|null
     */
    public static function getContent($html, $limit = 255, $endSubstitute = null, $lineBreak = true)
    {
        if (empty($html)) {
            return $html;
        }

        if($lineBreak) {
            $string = preg_replace('/\<br(\s*)?\/?\>/i', "\n", $html);
        }

        $string = strip_tags($string); //gets rid of the HTML
        if (strpos($string, '&') !== false) {
            $string = strip_tags(html_entity_decode($string));
        }

        if(empty($string) || mb_strlen($string) <= $limit) {
            if($lineBreak) {
                $string = nl2br($string);
            }

            return $string;
        }

        if($endSubstitute) {
            $limit -= mb_strlen($endSubstitute, 'UTF-8');
        }

        $stackCount = 0;
        while($limit > 0){
            $char = mb_substr($string, --$limit, 1, 'UTF-8');
            if(preg_match('#[^\p{L}\p{N}]#iu', $char)) {
                $stackCount++; //only alnum characters
            } elseif($stackCount > 0) {
                $limit++;
                break;
            }
        }

        $string = mb_substr($string, 0, $limit, 'UTF-8').$endSubstitute;
        if($lineBreak) {
            $string = nl2br($string);
        }

        return $string;
    }
}
