<?php

namespace Ecommage\CustomerReview\Model\Source;

use Magento\Framework\Data\OptionSourceInterface;
use Ecommage\CustomerReview\Model\ResourceModel\Review\CollectionFactory;

/**
 * Class Review
 *
 * @package Ecommage\CustomerReview\Model\Source
 */
class Review implements OptionSourceInterface
{
    /**
     * @var CollectionFactory
     */
    private $_collectionFactory;

    /**
     * Review constructor.
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
