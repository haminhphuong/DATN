<?php

namespace Ecommage\BannerManager\Block\Adminhtml\Banner\Edit;

use Magento\Backend\Block\Widget\Context;
use Ecommage\BannerManager\Model\Banner;

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
     * GenericButton constructor.
     *
     * @param Context $context
     */
    public function __construct(
        Context $context
    ) {
        $this->context = $context;
    }

    /**
     * @return string
     */
    public function getBackUrl()
    {
        return $this->getUrl('*/*/');
    }

    /**
     * @param string $route
     * @param array  $params
     *
     * @return string
     */
    public function getUrl($route = '', $params = [])
    {
        return $this->context->getUrlBuilder()->getUrl($route, $params);
    }

    /**
     * @return string
     */
    public function getDeleteUrl()
    {
        return $this->getUrl('*/*/delete', [Banner::BANNER_ID => $this->getId()]);
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->context->getRequest()->getParam(Banner::BANNER_ID);
    }
}
