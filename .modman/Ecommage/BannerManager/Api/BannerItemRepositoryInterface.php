<?php
namespace Ecommage\BannerManager\Api;

use Ecommage\BannerManager\Api\Data\ItemInterface;
use Magento\Framework\Api\SearchCriteriaInterface;

/**
 * Interface BannerRepositoryInterface
 *
 * @api
 */
interface BannerItemRepositoryInterface
{
    /**
     * Create or update a Banner item.
     *
     * @param ItemInterface $page
     * @return ItemInterface
     */
    public function save(ItemInterface $page);

    /**
     * Get a Banner item by Id
     *
     * @param int $id
     * @return ItemInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException If Banner with the specified ID does not exist.
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getById($id);

    /**
     * Retrieve Banners items which match a specified criteria.
     *
     * @param SearchCriteriaInterface $criteria
     */
    public function getList(SearchCriteriaInterface $criteria);

    /**
     * Delete a Banner item
     *
     * @param ItemInterface $page
     * @return ItemInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException If Banner with the specified ID does not exist.
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function delete(ItemInterface $page);

    /**
     * Delete a Banner item by Id
     *
     * @param int $id
     * @return ItemInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException If customer with the specified ID does not exist.
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function deleteById($id);
}
