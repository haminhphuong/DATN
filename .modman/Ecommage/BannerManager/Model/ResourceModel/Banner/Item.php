<?php

namespace Ecommage\BannerManager\Model\ResourceModel\Banner;

/**
 * Class Banner Item
 */
class Item extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    /**
     * Init
     */
    protected function _construct() // phpcs:ignore PSR2.Methods.MethodDeclaration
    {
        $this->_init('banner_item', 'entity_id');
    }
}
