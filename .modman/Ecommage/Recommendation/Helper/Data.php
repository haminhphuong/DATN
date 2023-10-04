<?php
namespace Ecommage\Recommendation\Helper;

use Magento\Catalog\Model\ProductRepository;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Store\Model\ScopeInterface;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\DataObjectFactory;

class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
    const RECOMMENDATION_PRODUCT_PAGE_ENABLE = 'recommendation/product_page/enable';
    const RECOMMENDATION_PRODUCT_PAGE_SCENARIOID_PEOPLEALSOVIEWED = 'recommendation/product_page/people_also_view/scenarioIdPeopleAlsoView';
    const RECOMMENDATION_PRODUCT_PAGE_SCENARIOID_SIMILARPRODUCT = 'recommendation/product_page/similar_product/scenarioIdSimilarProduct';
    const RECOMMENDATION_PRODUCT_LIST_SCENARIO_ID_RECOMMENDED_FOR_YOU = 'recommendation/product_list/recommended_for_you/scenarioIdRecommendedForYou';
    const RECOMMENDATION_PRODUCT_LIST_SCENARIO_ID_SIMILAR_RECENTLY = 'recommendation/product_list/similar_to_your_recently/scenarioIdSimilarRecently';
    const RECOMMENDATION_CHECKOUT_SUCCESS_SCENARIO_ID_RECOMMENDED_FOR_YOU = 'recommendation/checkout_success/recommended_for_you/scenarioIdRecommendedForYou';
    const RECOMMENDATION_CHECKOUT_SCENARIO_ID_COMPLEMENTARY_PRODUCTS = 'recommendation/checkout/complementary_products/scenarioIdComplementaryProducts';
    const RECOMMENDATION_PRODUCT_PAGE_NUMBER = 'recommendation/product_page/number';
    const RECOMMENDATION_PRODUCT_LIST_NUMBER = 'recommendation/product_list/number';
    const RECOMMENDATION_CHECKOUT_SUCCESS_NUMBER = 'recommendation/checkout_success/number';
    const RECOMMENDATION_CHECKOUT_NUMBER = 'recommendation/checkout/number';
    const RECOMMENDATION_CHECKOUT_SUCCESS_TITLE_RECOMMENDED_FOR_YOU = 'recommendation/checkout_success/recommended_for_you/titleRecommendedForYou';
    const RECOMMENDATION_CHECKOUT_TITLE_COMPLEMENTARY_PRODUCTS = 'recommendation/checkout/complementary_products/titleComplementaryProducts';
    const RECOMMENDATION_PRODUCT_PAGE_TITLE_PEOPLEALSOVIEWED = 'recommendation/product_page/people_also_view/titlePeopleAlsoView';
    const RECOMMENDATION_PRODUCT_PAGE_TITLE_SIMILARPRODUCT = 'recommendation/product_page/similar_product/titleSimilarProduct';
    const RECOMMENDATION_PRODUCT_LIST_TITLE_RECOMMENDED_FOR_YOU = 'recommendation/product_list/recommended_for_you/titleRecommendedForYou';
    const RECOMMENDATION_PRODUCT_LIST_TITLE_SIMILAR_RECENTLY = 'recommendation/product_list/similar_to_your_recently/titleSimilarRecently';

    /**
     * @var ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * @var StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var ProductRepository
     */
    protected $productRepository;
    /**
     * @var String
     */
    protected $title;
    /**
     * @var DataObjectFactory
     */
    protected $dataObjectFactory;

    /**
     * @param Context $context
     * @param ScopeConfigInterface $scopeConfig
     * @param StoreManagerInterface $storeManager
     */
    public function __construct
    (
        Context $context,
        ScopeConfigInterface $scopeConfig,
        StoreManagerInterface $storeManager,
        ProductRepository $productRepository,
        DataObjectFactory $dataObjectFactory
    )
    {
        parent::__construct($context);
        $this->storeManager = $storeManager;
        $this->scopeConfig = $scopeConfig;
        $this->productRepository = $productRepository;
        $this->dataObjectFactory = $dataObjectFactory;
    }

    /**
     * @return \Magento\Store\Api\Data\StoreInterface
     * @throws NoSuchEntityException
     */
    public function getStore(){
        return $this->storeManager->getStore();
    }

    /**
     * @return mixed
     */
    public function scenariaIdPeopleAlsoViewed()
    {
        return $this->scopeConfig->getValue(
            self::RECOMMENDATION_PRODUCT_PAGE_SCENARIOID_PEOPLEALSOVIEWED,
            ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * @return mixed
     */
    public function scenariaIdSimilarProduct()
    {
        return $this->scopeConfig->getValue(
            self::RECOMMENDATION_PRODUCT_PAGE_SCENARIOID_SIMILARPRODUCT,
            ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * @return mixed
     */
    public function titlePeopleAlsoViewed()
    {
        return $this->scopeConfig->getValue(
            self::RECOMMENDATION_PRODUCT_PAGE_TITLE_PEOPLEALSOVIEWED,
            ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * @return mixed
     */
    public function titleSimilarProduct()
    {
        return $this->scopeConfig->getValue(
            self::RECOMMENDATION_PRODUCT_PAGE_TITLE_SIMILARPRODUCT,
            ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * @return mixed
     */
    public function titleRecommendedForYou()
    {
        return $this->scopeConfig->getValue(
            self::RECOMMENDATION_PRODUCT_LIST_TITLE_RECOMMENDED_FOR_YOU,
            ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * @return mixed
     */
    public function titleSimilarRecently()
    {
        return $this->scopeConfig->getValue(
            self::RECOMMENDATION_PRODUCT_LIST_TITLE_SIMILAR_RECENTLY,
            ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * @return mixed
     */
    public function scenariaIdRecommendedForYou()
    {
        return $this->scopeConfig->getValue(
            self::RECOMMENDATION_PRODUCT_LIST_SCENARIO_ID_RECOMMENDED_FOR_YOU,
            ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * @return mixed
     */
    public function scenariaIdCheckoutSuccessRecommendedForYou()
    {
        return $this->scopeConfig->getValue(
            self::RECOMMENDATION_CHECKOUT_SUCCESS_SCENARIO_ID_RECOMMENDED_FOR_YOU,
            ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * @return mixed
     */
    public function scenariaIdCheckoutComplementaryProducts()
    {
        return $this->scopeConfig->getValue(
            self::RECOMMENDATION_CHECKOUT_SCENARIO_ID_COMPLEMENTARY_PRODUCTS,
            ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * @return mixed
     */
    public function titleCheckoutSuccessRecommendedForYou()
    {
        return $this->scopeConfig->getValue(
            self::RECOMMENDATION_CHECKOUT_SUCCESS_TITLE_RECOMMENDED_FOR_YOU,
            ScopeInterface::SCOPE_STORE
        );
    }
    /**
     * @return mixed
     */
    public function titleCheckoutComplementaryProducts()
    {
        return $this->scopeConfig->getValue(
            self::RECOMMENDATION_CHECKOUT_TITLE_COMPLEMENTARY_PRODUCTS,
            ScopeInterface::SCOPE_STORE
        );
    }
    /**
     * @return mixed
     */
    public function scenariaIdSimilarRecently()
    {
        return $this->scopeConfig->getValue(
            self::RECOMMENDATION_PRODUCT_LIST_SCENARIO_ID_SIMILAR_RECENTLY,
            ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * @return mixed
     */
    public function numProductList()
    {
        return $this->scopeConfig->getValue(
            self::RECOMMENDATION_PRODUCT_LIST_NUMBER,
            ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * @return mixed
     */
    public function numProductPage()
    {
        return $this->scopeConfig->getValue(
            self::RECOMMENDATION_PRODUCT_PAGE_NUMBER,
            ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * @return mixed
     */
    public function numCheckoutSuccess()
    {
        return $this->scopeConfig->getValue(
            self::RECOMMENDATION_CHECKOUT_SUCCESS_NUMBER,
            ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * @return mixed
     */
    public function numCheckout()
    {
        return $this->scopeConfig->getValue(
            self::RECOMMENDATION_CHECKOUT_NUMBER,
            ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * @return mixed
     */
    public function urlRecommendationProductPage()
    {
        return $this->_urlBuilder->getUrl('recommendation/productpage/recommendation');
    }

    /**
     * @return mixed
     */
    public function urlRecommendationProductList()
    {
        return $this->_urlBuilder->getUrl('recommendation/productlist/recommendation');
    }

    /**
     * @return mixed
     */
    public function urlRecommendationCheckoutSuccess()
    {
        return $this->_urlBuilder->getUrl('recommendation/checkoutsuccess/recommendation');
    }

    /**
     * @return mixed
     */
    public function urlRecommendationCheckout()
    {
        return $this->_urlBuilder->getUrl('recommendation/checkout/recommendation');
    }

    /**
     * @param $productId
     * @return \Magento\Catalog\Api\Data\ProductInterface|mixed|null
     * @throws NoSuchEntityException
     */
    public function getProduct($productId){
        return $this->productRepository->getById($productId);
    }

    /**
     * @param $title
     * @return void
     */
    public function setTitle($title){
        $this->title = $title;
    }

    /**
     * @return String
     */
    public function getTitle(){
        return $this->title;
    }

    public function getProductUrl($productId,$productUrl){
        $params = $this->_getRequest()->getParam('params');
        foreach ($params as $value){
            $parts = parse_url($value);
            parse_str($parts['path'], $query);
            if($productId == $query['rec_pid']){
                return $productUrl."?".$value;
            }
        }
        return $productUrl;
    }

    /**
     * @return mixed|null
     */
    public function getType(){
        return isset($this->_getRequest()->getParams()['type']) ? $this->_getRequest()->getParams()['type'] : null;
    }

    /**
     * @return mixed|null
     */
    public function getProductIds(){
        return isset($this->_getRequest()->getParams()['productIds']) ? $this->_getRequest()->getParams()['productIds'] : null;
    }

    /**
     * @param $html
     * @param $type
     * @return array|mixed|null
     */
    public function convertWebp($html,$type){
        $data = $this->dataObjectFactory->create(
            [
                'data' => [
                    'page' => $html,
                    'pageType' => $type
                ]
            ]
        );
        $this->_eventManager->dispatch('amoptimizer_process_ajax_page', ['data' => $data]);
        return $data->getData('page');
    }
}
