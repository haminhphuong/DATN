<?php

namespace Ecommage\BannerManager\Api\Data;

use Magento\Framework\Api\SearchResultsInterface;

/**
 * Interface for banner search results.
 * @api
 * @since 100.0.2
 */
interface BannerSearchResultsInterface extends SearchResultsInterface
{
    /**
     * Get banners list.
     *
     * @return \Ecommage\BannerManager\Api\Data\BannerInterface[]
     */
    public function getItems();

    /**
     * Set banners list.
     *
     * @param \Ecommage\BannerManager\Api\Data\BannerInterface[] $items
     * @return $this
     */
    public function setItems(array $items);
}
