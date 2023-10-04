<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Ecommage\CustomAmastyXsearch\Observer\Search;

use Ecommage\CustomAmastyXsearch\Helper\BlogData as HelperData;
use Magento\Catalog\Model\ResourceModel\Product\Collection;
use Magento\Framework\Event\Observer as EventObserver;
use Magento\Framework\Event\ObserverInterface;

/**
 * Class Product
 *
 * @package Ecommage\CustomAmastyXsearch\Observer\Search
 */
class Product implements ObserverInterface
{
    /**
     * @var HelperData
     */
    protected $_helperData;
    /**
     * @var \Magento\Catalog\Model\Product\Visibility
     */
    protected $productVisibility;
    /**
     * @var \Magento\Catalog\Model\Product\Attribute\Source\Status
     */
    protected $productStatus;
    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * Product constructor.
     * @param HelperData $helperData
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Catalog\Model\Product\Attribute\Source\Status $productStatus
     * @param \Magento\Catalog\Model\Product\Visibility $productVisibility
     */
    public function __construct(
        HelperData $helperData,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Catalog\Model\Product\Attribute\Source\Status $productStatus,
        \Magento\Catalog\Model\Product\Visibility $productVisibility
    )
    {
        $this->_helperData = $helperData;
        $this->_storeManager = $storeManager;
        $this->productStatus = $productStatus;
        $this->productVisibility = $productVisibility;
    }

    /**
     * @param EventObserver $observer
     *
     * @return $this|void
     */
    public function execute(EventObserver $observer)
    {
//        $websiteId = $this->_storeManager->getStore()->getWebsiteId();
        /** @var Collection $collection */
        $collection = $observer->getEvent()->getCollection();
//        $collection->getSelect()->joinLeft(
//            array('_inv' => $collection->getResource()->getTable('cataloginventory_stock_status')),
//            "_inv.product_id = e.entity_id and _inv.website_id=$websiteId",
//            array('stock_status')
//        );
//        $collection->addExpressionAttributeToSelect('in_stock', 'IFNULL(_inv.stock_status,0)', array());
//        $collection->getSelect()->reset('order');
//        $collection->getSelect()->order('in_stock DESC');
        if ((int)$this->_helperData->getStatusModule() === 1) {
            $pageSize = $collection->getPageSize();
            $collection->setPageSize(0);
            $collection->addAttributeToSelect(['new', 'sale'])->_loadAttributes()->setOrder('new')->setOrder('sale');
            $collection->unshiftOrder('relevance');
            $collection->setPageSize($pageSize);
        }

        return $this;
    }
}
