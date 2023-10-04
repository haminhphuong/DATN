<?php

namespace Ecommage\BannerManager\Model\System\Config;

use Magento\Framework\Option\ArrayInterface;
use Ecommage\BannerManager\Model\ResourceModel\Banner\CollectionFactory;

class BannerList implements ArrayInterface
{
    /**
     * @var array
     */
    private   $options = [];
    /**
     * @var CollectionFactory
     */
    protected $collectionFactory;

    /**
     * BannerList constructor.
     *
     * @param CollectionFactory $collectionFactory
     */
    public function __construct(CollectionFactory $collectionFactory)
    {
        $this->collectionFactory = $collectionFactory;
    }

    /**
     * @return array
     */
    public function toOptionArray()
    {
        if (empty($this->options)) {
            $collection = $this->collectionFactory->create();
            $this->options = $collection->toOptionArray();
            array_unshift($this->options, ['value' => '', 'label' => __('Please select a banner.')]);
        }

        return $this->options;
    }

    /**
     * @return array
     */
    public function toArray()
    {
        $options = [];
        if ($optionArray = $this->toOptionArray()) {
            foreach ($optionArray as $item) {
                $options[$item['value']] = $item['lable'];
            }
        }

        return $options;
    }
}
