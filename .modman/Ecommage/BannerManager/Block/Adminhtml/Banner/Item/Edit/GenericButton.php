<?php

namespace Ecommage\BannerManager\Block\Adminhtml\Banner\Item\Edit;

use Magento\Backend\Block\Widget\Context;
use Ecommage\BannerManager\Model\Banner\Item;
use Ecommage\BannerManager\Model\Banner\ItemFactory;

/**
 * Class GenericButton
 */
class GenericButton
{
    /**
     * @var Context
     */
    protected $context;
    /**
     * @var ItemFactory
     */
    protected $itemFactory;

    /**
     * GenericButton constructor.
     *
     * @param ItemFactory $itemFactory
     * @param Context     $context
     */
    public function __construct(
        ItemFactory $itemFactory,
        Context $context
    ) {
        $this->itemFactory = $itemFactory;
        $this->context     = $context;
    }

    /**
     * @return null
     */
    public function getItemId()
    {
        $id   = $this->getParam('id');
        $item = $this->getItemById($id);
        return $item->getEntityId() ?: null;
    }

    /**
     * @param      $key
     * @param null $default
     *
     * @return mixed
     */
    public function getParam($key, $default = null)
    {
        return $this->context->getRequest()->getParam($key, $default);
    }

    /**
     * @param $id
     *
     * @return Item
     */
    protected function getItemById($id)
    {
        return $this->itemFactory->create()->load($id);
    }

    /**
     * @return null
     */
    public function getBannerId()
    {
        $id   = $this->getParam('id');
        $item = $this->getItemById($id);
        return $item->getBannerId() ?: null;
    }

    /**
     * Generate url by route and parameters
     *
     * @param string $route
     * @param array  $params
     *
     * @return  string
     */
    public function getUrl($route = '', array $params = [])
    {
        return $this->context->getUrlBuilder()->getUrl($route, $params);
    }
}
