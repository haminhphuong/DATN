<?php


namespace Ecommage\WidgetStoreLocator\Model\Config;

use Amasty\Storelocator\Model\ResourceModel\Location\Collection;
use Ecommage\WidgetStoreLocator\Helper\Data;
use Magento\Framework\Data\OptionSourceInterface;


class WidgetStoreLocator implements OptionSourceInterface
{

    protected $locationCollection;

    protected $helperData;

    public function __construct(
        Collection $locationCollection,
        Data $helperData
    ) {
        $this->locationCollection = $locationCollection;
        $this->helperData = $helperData;
    }

    /**
     * @return array
     */
    public function toOptionArray()
    {
        $lists = [];
        $zipcodes =  array_unique($this->locationCollection->getColumnValues('zip'));
        $provinces = $this->helperData->getProvincesFromFile();
        foreach ($zipcodes as $zipcode){
            foreach($provinces as $province){
                if(stristr($province[2], $zipcode)){
                    $lists[] = [
                        'value' => $province[2],
                        'label' => $province[1]
                    ];
                }
            }
        }
        return $lists;
    }


}
