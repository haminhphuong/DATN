<?php

namespace Ecommage\SliderCollection\Model\Source;

use Magento\Framework\Data\OptionSourceInterface;
use Ecommage\SliderCollection\Model\ResourceModel\Slider\CollectionFactory;

/**
 * Class Slider
 *
 * @package Ecommage\SliderCollection\Model\Source
 */
class Slider implements OptionSourceInterface
{
    /**
     * @var CollectionFactory
     */
    private $_collectionFactory;

    /**
     * Slider constructor.
     *
     * @param CollectionFactory $collectionFactory
     */
    public function __construct(
        CollectionFactory $collectionFactory
    ) {
        $this->_collectionFactory = $collectionFactory;
    }

    /**
     * @return array
     */
    public function toOptionArray()
    {
        $slideshows = [];
        $collection = $this->_collectionFactory->create();
        foreach ($collection as $item) {
            if ($item->getIsActive()) {
                $slideshows[] = [
                    'value' => $item->getId(),
                    'label' => $item->getName()
                ];
            }
        }

        return $slideshows;
    }
}
