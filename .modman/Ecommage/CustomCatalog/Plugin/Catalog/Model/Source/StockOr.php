<?php

namespace Ecommage\CustomCatalog\Plugin\Catalog\Model\Source;

class StockOr
{
    /**
     * @param \Magento\CatalogInventory\Model\Source\Stock $subject
     * @param \Closure $proceed
     * @param \Magento\Eav\Model\Entity\Collection\AbstractCollection $collection
     * @param string $dir
     *
     * @return $this
     */
    public function aroundAddValueSortToCollection(
        $subject,
        $proceed,
        $collection,
        $dir
    ) {
        // fix magento bug. getting full table name
        $collection->getSelect()->order("stock_item_table.qty $dir");

        return $proceed($collection, $dir);
    }
}
