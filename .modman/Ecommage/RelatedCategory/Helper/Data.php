<?php

namespace Ecommage\RelatedCategory\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\UrlInterface;

class Data extends AbstractHelper
{
    /**
     * @var StoreManagerInterface
     */
    protected $_storeManager;
    public function __construct
    (
        Context $context,
        StoreManagerInterface $storeManager
    )
    {
        $this->_storeManager = $storeManager;
        parent::__construct($context);
    }
    public function getBaseUrlMedia(){
        return $this ->_storeManager-> getStore()->getBaseUrl(UrlInterface::URL_TYPE_MEDIA );
    }
}
