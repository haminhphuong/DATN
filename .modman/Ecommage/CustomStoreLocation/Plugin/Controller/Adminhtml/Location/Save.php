<?php

namespace Ecommage\CustomStoreLocation\Plugin\Controller\Adminhtml\Location;

use Amasty\Storelocator\Model\Location;
use Magento\Directory\Model\Region;
use Ecommage\CustomStoreLocation\Model\ResourceModel\City\Collection as CityCollection;

class Save
{
    /**
     * @var Location
     */
    protected $location;
    /**
     * @var Region
     */
    protected $region;
    /**
     * @var CityCollection
     */
    protected $cityCollection;

    public function __construct(Location $location, Region $region, CityCollection $cityCollection)
    {
        $this->location = $location;
        $this->region = $region;
        $this->cityCollection = $cityCollection;
    }

    public function beforeExecute(\Amasty\Storelocator\Controller\Adminhtml\Location\Save $subject)
    {
        if ($subject->getRequest()->getPostValue()){
            $addressFull = '';
            $dataPost = $subject->getRequest()->getPostValue();
            $stateId = isset($dataPost['state_id']) ? $dataPost['state_id'] : null;
            $cityId = isset($dataPost['city_id']) ? $dataPost['city_id'] : null;

            if ($cityId){
                $cityName = $this->getCityName($cityId);
                $addressFull .= $cityName;
            }
            if ($stateId){
                $stateName = $this->getStateNameById($stateId);
                $addressFull .= ' ' . $stateName;
            }

            $subject->getRequest()->setPostValue('address_full', $addressFull);
        }

    }

    public function getStateNameById($stateId)
    {

        $region = $this->region->load($stateId);
        if ($region->getId()){
            return $region->getDefaultName();
        }
        return '';
    }

    public function getCityName($cityId)
    {
        $city = $this->cityCollection->addFieldToFilter('city_id', ['eq' => $cityId])->getFirstItem();
        if ($city->getId()){
            return $city->getName();
        }
        return '';
    }
}
