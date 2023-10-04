<?php

namespace Ecommage\CustomStoreLocation\Helper;

use Amasty\Storelocator\Api\Validator\LocationProductValidatorInterface;
use Amasty\Storelocator\Model\ResourceModel\Location\Collection as LocationCollection;
use Amasty\Storelocator\Model\ResourceModel\Location\CollectionFactory;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\App\RequestInterface;
use Magento\Store\Model\ScopeInterface;
use Magento\Store\Model\Store;
use Magento\Store\Model\StoreManagerInterface;

/**
 *
 */
class Data extends AbstractHelper
{
    const AMLOCATOR_PATH             = "amlocator";
    const ECOMMAGE_PATH              = "ecommage";
    const MIN_LIMIT_PER_ADDRESS      = 3;
    const PER_ADDRESS                = "general/per_address";
    const HOTLINE_STORE              = "general/hotline";
    const HOTLINE_OWEN_CONTACT       = "contact/contact/hotline";
    const HOURS_OF_OPERATION_CONTACT = "contact/contact/time_contact";
    const STREET_ADDRESS_NORTH_OFFICE = "contact/contact/street_address_north_office";
    const STREET_ADDRESS_SOUTH_OFFICE = "contact/contact/street_address_south_office";

    /**
     * @var StoreManagerInterface
     */
    protected $_storeManager;
    /**
     * @var RequestInterface
     */
    protected $request;
    /**
     * @var ProductRepositoryInterface
     */
    protected $productRepository;
    /**
     * @var CollectionFactory
     */
    protected $locationCollectionFactory;
    /**
     * @var LocationProductValidatorInterface
     */
    protected $locationProductValidator;

    /**
     * @param Context $context
     * @param StoreManagerInterface $storeManager
     * @param RequestInterface $request
     * @param ProductRepositoryInterface $productRepository
     * @param CollectionFactory $locationCollectionFactory
     * @param LocationProductValidatorInterface $locationProductValidator
     */
    public function __construct
    (
        Context $context,
        StoreManagerInterface $storeManager,
        RequestInterface $request,
        ProductRepositoryInterface $productRepository,
        CollectionFactory $locationCollectionFactory,
        LocationProductValidatorInterface $locationProductValidator
    )
    {
        parent::__construct($context);
        $this->_storeManager = $storeManager;
        $this->request = $request;
        $this->productRepository = $productRepository;
        $this->locationCollectionFactory = $locationCollectionFactory;
        $this->locationProductValidator = $locationProductValidator;
    }

    /**
     * Get config
     *
     * @param string $path
     *
     * @return string
     */
    public function getConfig($path)
    {
        return $this->scopeConfig->getValue($path, \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
    }

    /**
     * @return string
     */
    public function getHotLineOwenContact()
    {
        return $this->getConfig(self::HOTLINE_OWEN_CONTACT);
    }

    /**
     * @return string
     */
    public function getHoursOfOperationOwenContact()
    {
        return $this->getConfig(self::HOURS_OF_OPERATION_CONTACT);
    }

    /**
     * @return string
     */
    public function getStreetAddressNorthOfficeOwenContact()
    {
        return $this->getConfig(self::STREET_ADDRESS_NORTH_OFFICE);
    }

    /**
     * @return string
     */
    public function getStreetAddressSouthOfficeOwenContact()
    {
        return $this->getConfig(self::STREET_ADDRESS_SOUTH_OFFICE);
    }

    /**
     * @return int
     */
    public function getPerAddress()
    {
        return (int)$this->getAmlocatorConfig(self::PER_ADDRESS) ?: self::MIN_LIMIT_PER_ADDRESS;
    }

    /**
     * @return mixed
     */
    public function getHotline()
    {
        return $this->getEcommageConfig(self::HOTLINE_STORE);
    }

    /**
     * @param $path
     *
     * @return mixed
     */
    public function getAmlocatorConfig($path)
    {
        return $this->scopeConfig->getValue(
            self::AMLOCATOR_PATH . '/' . $path,
            ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * @param $path
     *
     * @return mixed
     */
    public function getEcommageConfig($path)
    {
        return $this->scopeConfig->getValue(
            self::ECOMMAGE_PATH . '/' . $path,
            ScopeInterface::SCOPE_STORE
        );
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
        $store = $this->_storeManager->getStore(true)->getId();
        $attributesFromRequest = [];

        $select = $locationCollection->getSelect();
        if (!$this->_storeManager->isSingleStoreMode()) {
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
        $store = $this->_storeManager->getStore(true)->getId();
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

    /**
     * @return LocationCollection
     */
    public function createLocation(){
        return $this->locationCollectionFactory->create();
    }
}
