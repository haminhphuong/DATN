<?php

namespace Ecommage\SliderCategoryCollection\Ui\Component;

use Ecommage\SliderCategoryCollection\Model\ResourceModel\Slider\CollectionFactory;

/**
 * Class DataProvider
 *
 * @package Ecommage\BackgroundSlideshow\Ui\Component
 */
class DataProvider extends \Magento\Ui\DataProvider\AbstractDataProvider
{
    /**
     * @var CollectionFactory
     */
    protected $collection;

    /**
     * @var \Magento\Ui\DataProvider\AddFieldToCollectionInterface[]
     */
    protected $addFieldStrategies;

    /**
     * @var \Magento\Ui\DataProvider\AddFilterToCollectionInterface[]
     */
    protected $addFilterStrategies;

    /**
     * DataProvider
     *
     * @param string                                                    $name
     * @param string                                                    $primaryFieldName
     * @param string                                                    $requestFieldName
     * @param CollectionFactory                                         $collectionFactory
     * @param \Magento\Ui\DataProvider\AddFieldToCollectionInterface[]  $addFieldStrategies
     * @param \Magento\Ui\DataProvider\AddFilterToCollectionInterface[] $addFilterStrategies
     * @param array                                                     $meta
     * @param array                                                     $data
     *
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function __construct(
        $name,
        $primaryFieldName,
        $requestFieldName,
        CollectionFactory $collectionFactory,
        array $addFieldStrategies = [],
        array $addFilterStrategies = [],
        array $meta = [],
        array $data = []
    ) {
        parent::__construct($name, $primaryFieldName, $requestFieldName, $meta, $data);
        $this->collection          = $collectionFactory->create();
        $this->addFieldStrategies  = $addFieldStrategies;
        $this->addFilterStrategies = $addFilterStrategies;
    }
}
