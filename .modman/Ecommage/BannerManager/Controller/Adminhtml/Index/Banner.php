<?php

namespace Ecommage\BannerManager\Controller\Adminhtml\Index;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\App\Request\DataPersistorInterface;
use Magento\Framework\View\Result\PageFactory;
use Ecommage\BannerManager\Model\BannerFactory;

abstract class Banner extends Action
{
    const ADMIN_RESOURCE = 'Ecommage_BannerManager::banner';
    /**
     * @var DataPersistorInterface
     */
    protected $dataPersistor;
    /**
     * @var BannerFactory
     */
    protected $_bannerFactory;
    /**
     * @var PageFactory
     */
    protected $_resultPageFactory;

    /**
     * Banner constructor.
     *
     * @param Context                $context
     * @param BannerFactory          $bannerFactory
     * @param DataPersistorInterface $dataPersistor
     * @param PageFactory            $resultPageFactory
     */
    public function __construct(
        Context $context,
        BannerFactory $bannerFactory,
        DataPersistorInterface $dataPersistor,
        PageFactory $resultPageFactory
    ) {
        $this->dataPersistor      = $dataPersistor;
        $this->_bannerFactory     = $bannerFactory;
        $this->_resultPageFactory = $resultPageFactory;
        parent::__construct($context);
    }
}
