<?php

namespace Ecommage\CustomCms\Block;

use Magento\Framework\UrlInterface;
use Magento\Framework\View\Element\Template;

class Canonical extends Template
{
    /**
     * @var UrlInterface
     */
    protected $_url;

    /**
     * Canonical constructor.
     *
     * @param Template\Context $context
     * @param UrlInterface     $url
     * @param array            $data
     */
    public function __construct(
        Template\Context $context,
        UrlInterface $url,
        array $data = []
    ) {
        $this->_url = $url;
        parent::__construct($context, $data);
    }

    /**
     * @return string
     */
    public function getCurrentUrl()
    {
        return rtrim(strtok($this->_url->getCurrentUrl(), '?'), '/');
    }
}
