<?php

namespace Ecommage\BannerManager\Block;

use Magento\Framework\View\Element\Template;
use Ecommage\BannerManager\Model\ResourceModel\Banner\Item\CollectionFactory;

class Item extends Template
{
    /**
     * @var CollectionFactory
     */
    public $collection;

    /**
     * Item constructor.
     * @param CollectionFactory $collectionFactory
     * @param Template\Context $context
     * @param array $data
     */
    public function __construct(
        CollectionFactory $collectionFactory,
        Template\Context $context, array $data = []
    )
    {
        $this->collection = $collectionFactory;
        parent::__construct($context, $data);
    }

    /**
     * @return \Ecommage\BannerManager\Model\ResourceModel\Banner\Item\Collection
     */
    public function getItems(){
        $timeNow = (new \DateTime())->format(\Magento\Framework\Stdlib\DateTime::DATETIME_PHP_FORMAT);
        $items = $this->collection->create();
        $items->addFilter('is_active',1);
        $items->addFieldToFilter('start_date',[
            ['null'=> true],
            ['lt'=>$timeNow]
        ])
        ->addFieldToFilter('end_date',[
            ['null' => true],
            ['gt'=>$timeNow]
        ]);

        return $items;
    }
}
