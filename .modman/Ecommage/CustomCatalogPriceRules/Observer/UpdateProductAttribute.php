<?php

namespace Ecommage\CustomCatalogPriceRules\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\CatalogInventory\Model\Stock\Item;
use Ecommage\CustomCatalogPriceRules\Helper\Data;
use Magento\Framework\Exception\CouldNotSaveException;

/**
 * Class UpdateProduct
 */
class UpdateProductAttribute implements ObserverInterface
{
    /**
     * @var Data
     */
    protected $helperData;

    /**
     * UpdateProductAttribute constructor.
     *
     * @param Data $helperData
     */
    public function __construct(Data $helperData)
    {
        $this->helperData = $helperData;
    }

    /**
     * @param Observer $observer
     *
     * @throws CouldNotSaveException
     */
    public function execute(Observer $observer)
    {
        /** @var Item $item */
        $item = $observer->getEvent()->getItem();
        $this->helperData->indexRow($item->getProductId());
    }
}
