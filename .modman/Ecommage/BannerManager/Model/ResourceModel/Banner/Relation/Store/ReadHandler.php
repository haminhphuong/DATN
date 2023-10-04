<?php
/**
 * Copyright © Thomas Nguyen, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Ecommage\BannerManager\Model\ResourceModel\Banner\Relation\Store;

use Ecommage\BannerManager\Model\ResourceModel\Banner;
use Magento\Framework\EntityManager\Operation\ExtensionInterface;

/**
 * Class ReadHandler
 */
class ReadHandler implements ExtensionInterface
{
    /**
     * @var Banner
     */
    protected $resourceBanner;

    /**
     * @param Banner $resourceBanner
     */
    public function __construct(
        Banner $resourceBanner
    ) {
        $this->resourceBanner = $resourceBanner;
    }

    /**
     * @param object $entity
     * @param array $arguments
     * @return object
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function execute($entity, $arguments = [])
    {
        if ($entity->getId()) {
            $stores = $this->resourceBanner->lookupStoreIds((int)$entity->getId());
            $entity->setData('store_id', $stores);
            $entity->setData('stores', $stores);
        }
        return $entity;
    }
}
