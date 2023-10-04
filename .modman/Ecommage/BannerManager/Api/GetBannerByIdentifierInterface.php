<?php
namespace Ecommage\BannerManager\Api;

/**
 * Command to load the banner data by specified identifier
 * @api
 * @since 103.0.0
 */
interface GetBannerByIdentifierInterface
{
    /**
     * Load banner data by given banner identifier.
     *
     * @param string $identifier
     * @param int $storeId
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @return \Ecommage\BannerManager\Api\Data\BannerInterface
     * @since 103.0.0
     */
    public function execute(string $identifier, int $storeId) : \Ecommage\BannerManager\Api\Data\BannerInterface;
}
