<?php

namespace Ecommage\RelatedCategory\Block\Adminhtml\Grid\Renderer;

use Magento\Backend\Block\Widget\Grid\Column\Renderer\AbstractRenderer;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\UrlInterface;
class Image extends AbstractRenderer
{
    private $_storeManager;
    /**
     * @param \Magento\Backend\Block\Context $context
     * @param array $data
     */
    public function __construct
    (
        \Magento\Backend\Block\Context $context,
        StoreManagerInterface $storemanager,
        array $data = []
    )
    {
        $this->_storeManager = $storemanager;
        parent::__construct($context, $data);
        $this->_authorization = $context->getAuthorization();
    }
    /**
     * Renders grid column
     *
     * @param Object $row
     * @return  string
     */
    public function render(Object $row)
    {
        $mediaDirectory = $this->_storeManager->getStore()->getBaseUrl(UrlInterface::URL_TYPE_MEDIA);
        $imageUrl = $mediaDirectory.$this->_getValue($row);
        return '<img src="'.$imageUrl.'" width="50"/>';
    }
}
