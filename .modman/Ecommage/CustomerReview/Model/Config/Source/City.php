<?php

namespace Ecommage\CustomerReview\Model\Config\Source;

use Magento\Directory\Model\ResourceModel\Region\CollectionFactory;

class City implements \Magento\Framework\Data\OptionSourceInterface
{
    const COUNTRY_ID = 'VN';
    /**
     * @var CollectionFactory
     */
    protected $collectionFactory;

    /**
     * City constructor.
     *
     * @param CollectionFactory $collectionFactory
     */
    public function __construct(
        CollectionFactory $collectionFactory
    )
    {
        $this->collectionFactory = $collectionFactory;
    }

    public function toOptionArray()
    {
        $options = [];
        foreach ($this->_getCollection() as $item) {
            $options[] = [
                'value' => $item->getRegionId(),
                'label' => $item->getDefaultName()
            ];
        }

        if (count($options) > 0) {
            array_unshift(
                $options,
                ['title' => '', 'value' => '', 'label' => __('Please select a city')]
            );
        }
        return $options;
    }

    /**
     * @return mixed
     */
    protected function _getCollection()
    {
        return $this->collectionFactory->create()->addFieldToFilter('country_id', self::COUNTRY_ID);
    }
}
