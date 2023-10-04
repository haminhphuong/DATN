<?php

namespace Ecommage\CustomCatalogPriceRules\Plugin;

use Ecommage\CustomCatalogPriceRules\Helper\Data;

/**
 * Class CatalogRule
 */
class CatalogRule
{
    /**
     * @var Data
     */
    protected $helperData;

    /**
     * CatalogRule constructor.
     *
     * @param Data $helperData
     */
    public function __construct(
        Data $helperData
    ) {
        $this->helperData = $helperData;
    }

    /**
     * @param       $subject
     * @param       $id
     * @param false $forceReindex
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function beforeReindexRow($subject, $id, $forceReindex = false)
    {
        $this->helperData->indexRow($id);
    }

    /**
     * @param       $subject
     * @param       $ids
     * @param false $forceReindex
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function beforeReindexList($subject, $ids, $forceReindex = false)
    {
        foreach ($ids as $id) {
            $this->helperData->indexRows($id);
        }
    }
}
