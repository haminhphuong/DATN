<?php
namespace Ecommage\CustomMegaMenu\Helper;
use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Store\Model\StoreManagerInterface;
use Codazon\MegaMenu\Model\MegamenuFactory;
use Codazon\MegaMenu\Block\Widget\Megamenu;
use Magento\Theme\Block\Html\Topmenu;

class Data extends AbstractHelper
{
    /**
     * @var StoreManagerInterface
     */
    protected $storeManager;
    /**
     * @var MegamenuFactory
     */
    protected $megamenuFactory;

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * @param Context $context
     * @param StoreManagerInterface $storeManager
     * @param MegamenuFactory $megamenuFactory
     */
    public function __construct
    (
        Context $context,
        StoreManagerInterface $storeManager,
        MegamenuFactory $megamenuFactory,
        \Magento\Framework\View\Layout $layout
    )
    {
        $this->storeManager = $storeManager;
        $this->megamenuFactory = $megamenuFactory;
        $this->layout = $layout;
        $this->scopeConfig = $context->getScopeConfig();
        parent::__construct($context);
    }

    /**
     * @return int
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getStore(){
        return $this->storeManager->getStore()->getId();
    }

    /**
     * @return mixed
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getMegaMenu(){
        $megaFactory = $this->megamenuFactory->create();
        return $megaFactory->getCollection()
            ->addFieldToFilter('is_active',1)
            ->addFieldToFilter('store_id', [0, $this->getStore()])->getFirstItem();
    }


    public function getWidgetMegaMenu(){
       return $this->layout->createBlock('Codazon\MegaMenu\Block\Widget\Megamenu');
    }


    public function getTopMenu(){
        return $this->layout->createBlock('Magento\Theme\Block\Html\Topmenu');
    }

    /**
     * @return mixed
     */
    public function getValue(){
        return $this->scopeConfig->getValue('codazon_megamenu/general/disable_default_menu');
    }
}
