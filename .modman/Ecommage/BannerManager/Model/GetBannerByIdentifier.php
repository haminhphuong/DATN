<?php
/**
 * Copyright © Thomas Nguyen, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Ecommage\BannerManager\Model;

use Ecommage\BannerManager\Api\GetBannerByIdentifierInterface;
use Ecommage\BannerManager\Api\Data\BannerInterface;
use Magento\Framework\Exception\NoSuchEntityException;

/**
 * Class GetBannerByIdentifier
 */
class GetBannerByIdentifier implements GetBannerByIdentifierInterface
{
    /**
     * @var \Ecommage\BannerManager\Model\BannerFactory
     */
    private $bannerFactory;

    /**
     * @var ResourceModel\Banner
     */
    private $bannerResource;

    /**
     * @param BannerFactory $bannerFactory
     * @param ResourceModel\Banner $bannerResource
     */
    public function __construct(
        \Ecommage\BannerManager\Model\BannerFactory $bannerFactory,
        \Ecommage\BannerManager\Model\ResourceModel\Banner $bannerResource
    ) {
        $this->bannerFactory = $bannerFactory;
        $this->bannerResource = $bannerResource;
    }

    /**
     * @inheritdoc
     */
    public function execute(string $identifier, int $storeId) : BannerInterface
    {
        $banner = $this->bannerFactory->create();
        $banner->setStoreId($storeId);
        $this->bannerResource->load($banner, $identifier, BannerInterface::IDENTIFIER);

        if (!$banner->getId()) {
            throw new NoSuchEntityException(__('The CMS banner with the "%1" ID doesn\'t exist.', $identifier));
        }

        return $banner;
    }
}
