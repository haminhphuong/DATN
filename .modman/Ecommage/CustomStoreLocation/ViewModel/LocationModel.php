<?php

namespace Ecommage\CustomStoreLocation\ViewModel;

use Amasty\Storelocator\Api\Validator\LocationProductValidatorInterface;
use Amasty\Storelocator\Model\ConfigProvider;
use Amasty\Storelocator\Model\ResourceModel\Attribute\Collection as AttributeCollection;
use Amasty\Storelocator\Model\ResourceModel\Location\Collection as LocationCollection;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Registry;
use Magento\Store\Model\ScopeInterface;
use Amasty\Storelocator\Model\ResourceModel\Location\CollectionFactory;
use Ecommage\Address\Model\CityRepository;
use Magento\Directory\Model\RegionFactory;
use Magento\Store\Model\Store;
use Magento\Framework\DataObject;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Framework\Json\DecoderInterface;
use Magento\Framework\Json\EncoderInterface;

class LocationModel implements \Magento\Framework\View\Element\Block\ArgumentInterface
{
    /**
     * @var ScopeConfigInterface
     */
    private $scopeConfig;

    /**
     * @var \Magento\Directory\Block\Data
     */
    protected $directoryData;

    /**
     * @var \Magento\Directory\Helper\Data
     */
    protected $helperData;

    /**
     * @var \Magento\Framework\App\RequestInterface
     */
    protected $request;

    /**
     * @var CollectionFactory
     */
    private $locationCollectionFactory;

    /**
     * @var CityRepository
     */
    private $cityRepository;

    /**
     * @var RegionFactory
     */
    private $regionFactory;
    /**
     * @var Registry
     */
    protected $coreRegistry;
    /**
     * @var AttributeCollection
     */
    protected $attributeCollection;
    /**
     * @var DataObject
     */
    protected $dataObject;
    /**
     * @var array
     */
    protected $attributeIds;
    /**
     * @var ConfigProvider
     */
    protected $configProvider;
    /**
     * @var StoreManagerInterface
     */
    protected $storeManager;
    /**
     * @var ProductRepositoryInterface
     */
    protected $productRepository;
    /**
     * @var LocationProductValidatorInterface
     */
    protected $locationProductValidator;
    /**
     * @var DecoderInterface
     */
    protected $decoderInterface;
    /**
     * @var EncoderInterface
     */
    protected $encoderInterface;

    /**
     * @param ScopeConfigInterface $scopeConfig
     * @param \Magento\Directory\Block\Data $directoryData
     * @param \Magento\Directory\Helper\Data $helperData
     * @param \Magento\Framework\App\RequestInterface $request
     * @param CollectionFactory $collectionFactory
     * @param CityRepository $cityRepository
     * @param RegionFactory $regionFactory
     * @param Registry $coreRegistry
     * @param AttributeCollection $attributeCollection
     * @param DataObject $dataObject
     * @param ConfigProvider $configProvider
     * @param StoreManagerInterface $storeManager
     * @param ProductRepositoryInterface $productRepository
     * @param LocationProductValidatorInterface $locationProductValidator
     * @param DecoderInterface $decoderInterface
     * @param EncoderInterface $encoderInterface
     */
    public function __construct(
        ScopeConfigInterface $scopeConfig,
        \Magento\Directory\Block\Data $directoryData,
        \Magento\Directory\Helper\Data $helperData,
        \Magento\Framework\App\RequestInterface $request,
        CollectionFactory $collectionFactory,
        CityRepository $cityRepository,
        RegionFactory $regionFactory,
        Registry $coreRegistry,
        AttributeCollection $attributeCollection,
        DataObject $dataObject,
        ConfigProvider $configProvider,
        StoreManagerInterface $storeManager,
        ProductRepositoryInterface $productRepository,
        LocationProductValidatorInterface $locationProductValidator,
        DecoderInterface $decoderInterface,
        EncoderInterface $encoderInterface
    ) {
        $this->scopeConfig               = $scopeConfig;
        $this->directoryData             = $directoryData;
        $this->helperData                = $helperData;
        $this->request                   = $request;
        $this->locationCollectionFactory = $collectionFactory;
        $this->cityRepository            = $cityRepository;
        $this->regionFactory             = $regionFactory;
        $this->coreRegistry = $coreRegistry;
        $this->attributeCollection = $attributeCollection;
        $this->dataObject = $dataObject;
        $this->configProvider = $configProvider;
        $this->storeManager = $storeManager;
        $this->productRepository = $productRepository;
        $this->locationProductValidator = $locationProductValidator;
        $this->decoderInterface = $decoderInterface;
        $this->encoderInterface = $encoderInterface;
    }

    /**
     * @param string|null $scopeCode
     *
     * @return string
     */
    public function isUseGoogleMap($scopeCode = ScopeInterface::SCOPE_STORE)
    {
        return $this->scopeConfig->getValue("amlocator/general/use_google_map", $scopeCode);
    }

    /**
     * @return mixed
     * @SuppressWarnings(PHPMD.StaticAccess)
     */
    public function renderGoogleMap()
    {
        $_objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        return $_objectManager->get('Amasty\Storelocator\Block\Location')->setTemplate('Amasty_Storelocator::center.phtml')->toHtml();
    }

    /**
     * @return string
     */
    public function getCountryHtmlSelect()
    {
        return $this->directoryData->getCountryHtmlSelect();
    }

    /**
     * Retrieve regions data json
     *
     * @return string
     * @throws NoSuchEntityException
     */
    public function dataGetRegionJson($locationCollection)
    {
        $state = [];
        $region = $this->decoderInterface->decode($this->helperData->getRegionJson());
        foreach ($locationCollection as $location){
            $state[] = $location->getState();
        }
        if(!empty($state)){
            $result = array_flip($state);
            $diffRegionVN = array_diff_key($region['VN'], $result);
            foreach ($diffRegionVN as $key=>$val){
                unset($region['VN'][$key]);
            }
        }
        return $this->encoderInterface->encode($region);
    }

    /**
     * Return ISO2 country codes, which have optional Zip/Postal pre-configured
     *
     * @param bool $asJson
     *
     * @return array|string
     */
    public function dataGetCountriesWithOptionalZip($asJson)
    {
        return $this->helperData->getCountriesWithOptionalZip($asJson);
    }

    /**
     * @return string
     */
    public function getAmStoreSearchUrl()
    {
        return $this->directoryData->getUrl("amlocator/index/search");
    }

    /**
     * @param $collection
     *
     * @return mixed
     */
    public function searchByColletion()
    {
        $collection = $this->getLocationCollection();
        $collection->joinScheduleTable();

        $country = $this->request->getParam("country");
        $state   = $this->request->getParam("region");
        $city    = $this->request->getParam("city");

        $collection->addFieldToFilter('country', $country);
        if ($state) {
            $collection->addFieldToFilter('state', $state);
        }

        if ($city) {
            $collection->addFieldToFilter('city', $city);
        }

        return $collection;
    }

    /**
     * @param $lat
     * @param $lng
     *
     * @return string
     */
    public function getGoogleMapByLatLng($lat, $lng)
    {
        return 'https://www.google.com/maps/place/' . $lat . ',' . $lng;
    }

    /**
     * @param $cityId
     *
     * @return string
     */
    public function getCityName($cityId)
    {
        $cityName = $this->cityRepository->getById($cityId)->getName();
        return $cityName ? $cityName : '';
    }

    /**
     * @param $stateId
     *
     * @return mixed
     */
    public function getStateName($stateId)
    {
        $stateName = $this->regionFactory->create()->load($stateId)->getName();

        return $stateName ? $stateName : '';
    }

    /**
     * @return LocationCollection
     */
    public function getLocationCollection()
    {
        $pageNumber = (int)$this->request->getParam('p') ? (int)$this->request->getParam('p') : 1;
        $productId = (int)$this->request->getParam('product_id');

        $locationCollection = $this->locationCollectionFactory->create();
        $locations = $this->getFilterbyProduct($locationCollection, $productId);

        if ($attributesData = $this->prepareWidgetAttributes()) {
            $locations->clear();
            $locations->applyAttributeFilters($attributesData);
        }
        $locations->setCurPage($pageNumber);
        $locations->setPageSize($this->configProvider->getPaginationLimit());

        return $locations;
    }

    /**
     * @return array
     */
    public function prepareWidgetAttributes()
    {
        $params = [];
        foreach ($this->dataObject->getData() as $key => $value) {
            if (in_array($key, $this->getAttributeIds())) {
                $params[$key] = explode(',', $value);
            }
        }

        return $params;
    }

    /**
     * Get attribute ids
     *
     * @return array
     */
    public function getAttributeIds()
    {
        if (!$this->attributeIds) {
            $this->attributeIds = $this->attributeCollection->getAllIds();
        }
        return $this->attributeIds;
    }

    /**
     * @param $locationCollection
     * @param $productId
     * @return LocationCollection
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getFilterbyProduct($locationCollection, $productId)
    {
        /**
         * @var LocationCollection $locationCollection
         */
        $store = $this->storeManager->getStore(true)->getId();
        $attributesFromRequest = [];

        $select = $locationCollection->getSelect();
        if (!$this->storeManager->isSingleStoreMode()) {
            $locationCollection->addFilterByStores([Store::DEFAULT_STORE_ID, $store]);
        }

        $select->where('main_table.status = 1');
        $locationCollection->addDistance($select);

        $params = $this->request->getParams();
        if (isset($params['attributes'])) {
            $attributesFromRequest = $locationCollection->prepareRequestParams($params['attributes']);
        }

        $locationCollection->applyAttributeFilters($attributesFromRequest);

        $locations = $this->filterLocationsByProduct($productId);

        return $locations;
    }

    /**
     * @param $productId
     * @param $storeIds
     * @return LocationCollection
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function filterLocationsByProduct($productId)
    {
        $locationIds = [];

        $product = $this->productRepository->getById($productId);

        $locationCollection = $this->locationCollectionFactory->create();
        $store = $this->storeManager->getStore(true)->getId();
        $stores = ['0'];
        if ($store != 0) {
            $stores = ['0', $store];
        }

        $locationCollection->addFilterByStores($stores);

        foreach ($locationCollection->getItems() as $item) {
            if ($this->locationProductValidator->isValid($item, $product)) {
                $locationIds[] = $item->getId();
            }
        }
        $locationCollection->clear();
        $locationCollection->addFieldToFilter('main_table.id', ['in' => $locationIds]);
        return $locationCollection;
    }
}
