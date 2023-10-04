<?php

namespace Ecommage\WidgetStoreLocator\Block\Widget;

use Amasty\Storelocator\Model\LocationFactory;
use Amasty\Storelocator\Model\ResourceModel\Location\Collection;
use Amasty\Storelocator\Model\ResourceModel\Location\CollectionFactory;
use Ecommage\WidgetStoreLocator\Helper\Data;
use Magento\Framework\View\Element\Template;
use Magento\Widget\Block\BlockInterface;
use Magento\Store\Model\StoreManagerInterface;

/**
 * Class Slider
 *
 * @package Ecommage\WidgetStoreLocator\Block\Widget
 */
class StoreLocator extends Template implements BlockInterface
{
    /**
     * @var string
     */
    protected $_template = 'Ecommage_WidgetStoreLocator::widget/store-locator.phtml';
    /**
     * @var null
     */
    protected $locations = null;
    /**
     * @var null
     */
    protected $mapLocations = null;
    /**
     * @var Data
     */
    protected $helperData;
    /**
     * @var LocationFactory
     */
    protected $locationFactory;
    /**
     * @var CollectionFactory
     */
    protected $collectionFactory;

    /**
     * StoreLocator constructor.
     *
     * @param CollectionFactory $collectionFactory
     * @param Template\Context  $context
     * @param array             $data
     */
    public function __construct(
        CollectionFactory $collectionFactory,
        LocationFactory $locationFactory,
        Template\Context $context,
        Data $helperData,
        array $data = []
    ) {
        $this->helperData        = $helperData;
        $this->locationFactory   = $locationFactory;
        $this->collectionFactory = $collectionFactory;
        parent::__construct($context, $data);
    }

    /**
     * @return array
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    protected function loadLocations()
    {
        if (!$this->locations) {
            $cacheKey  = sha1(json_encode($this->getData()));
            $locations = $this->_cache->load($cacheKey);
            if (!empty($locations)) {
                $locationData = json_decode($locations, true);
                foreach ($locationData as $key => $items) {
                    foreach ($items as $data) {
                        $item = $this->locationFactory->create();
                        $item->setData($data);
                        $this->locations[$key][] = $item;
                    }
                }

                return $this->locations;
            }

            $locations = [];
            $storeId   = $this->_storeManager->getStore()->getId();
            $storeIds  = [$storeId, 0];
            /** @var \Amasty\Storelocator\Model\ResourceModel\Location\Collection $collection */
            $collection = $this->collectionFactory->create();
            $collection->addFieldToFilter('status', 1);
            $collection->addFilterByStores($storeIds);
            $collection->setOrder('position', 'ASC');
            $collection->getSelect()->joinLeft(
                ['sc' => $collection->getTable('amasty_amlocator_store_attribute')],
                'main_table.id = sc.store_id',
                [
                    'service_center' => 'sc.value'
                ]
            );

            $cacheData = [];
            /** @var \Amasty\Storelocator\Model\Location $location */
            foreach ($collection as $location) {
                $itemData = [
                    'name'           => $location->getData('name'),
                    'address'        => $location->getData('address'),
                    'service_center' => $location->getData('service_center'),
                    'phone'          => $location->getData('phone'),
                    'zip'            => $location->getData('zip')
                ];
                $itemObject = $location->setData($itemData);
                $zip      = $location->getData('zip');
                if ((int)$location->getData('service_center')) {
                    $locations['services'][] = $itemObject;
                    $cacheData['services'][] = $itemData;
                }

                $locations['all'][] = $itemObject;
                $locations[$zip][]  = $itemObject;
                $cacheData['all'][] = $itemData;
                $cacheData[$zip][]  = $itemData;
            }

            $cacheData = json_encode($cacheData);
            $this->_cache->save($cacheData, $cacheKey);
            $this->locations = $locations;
        }

        return (array)$this->locations;
    }

    /**
     * @return array
     */
    protected function loadMapLocations()
    {
        if (!$this->mapLocations) {
            $mapLocations = [];
            $provinces    = $this->helperData->getProvincesFromFile();
            foreach ($provinces as $province) {
                $zip                = $province[2];
                $mapLocations[$zip] = $province[1];
            }
            $this->mapLocations = $mapLocations;
        }

        return (array)$this->mapLocations;
    }

    /**
     * @return array
     */
    public function getProvincesToShow()
    {
        $items    = [];
        $zipcodes = $this->getData('store_locator');
        if (!empty($zipcodes)) {
            $this->loadMapLocations();
            $zipcodes = explode(",", $zipcodes);
            foreach ($zipcodes as $stt => $zipcode) {
                if (!empty($this->mapLocations[$zipcode])) {
                    $name    = $this->mapLocations[$zipcode];
                    $items[] = [
                        'stt'     => $stt + 1,
                        'name'    => $name,
                        'zipcode' => $zipcode
                    ];
                }
            }
        }

        return $items;
    }

    /**
     * @return array
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getAllStores()
    {
        $this->loadLocations();
        return $this->locations['all'] ?? [];
    }

    /**
     * @param $zip
     *
     * @return array
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getStoresLocator($zip)
    {
        $this->loadLocations();
        return $this->locations[$zip] ?? [];
    }

    /**
     * @return array
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getServiceCenter()
    {
        $this->loadLocations();
        return $this->locations['services'] ?? [];
    }
}
