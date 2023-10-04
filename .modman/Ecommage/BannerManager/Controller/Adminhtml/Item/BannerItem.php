<?php

namespace Ecommage\BannerManager\Controller\Adminhtml\Item;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Registry;
use Magento\Framework\View\Result\PageFactory;
use Ecommage\BannerManager\Model\Banner\ItemFactory;
use Magento\Framework\App\Request\DataPersistorInterface;

abstract class BannerItem extends Action
{
    const ADMIN_RESOURCE = 'Ecommage_BannerManager::banner';
    /**
     * @var ItemFactory
     */
    protected $_itemFactory;
    /**
     * @var DataPersistorInterface
     */
    protected $dataPersistor;
    /**
     * @var PageFactory
     */
    protected $_resultPageFactory;

    /**
     * BannerItem constructor.
     *
     * @param Context                $context
     * @param ItemFactory            $itemFactory
     * @param DataPersistorInterface $dataPersistor
     * @param PageFactory            $resultPageFactory
     */
    public function __construct(
        Context $context,
        ItemFactory $itemFactory,
        DataPersistorInterface $dataPersistor,
        PageFactory $resultPageFactory
    ) {
        $this->dataPersistor      = $dataPersistor;
        $this->_itemFactory       = $itemFactory;
        $this->_resultPageFactory = $resultPageFactory;
        parent::__construct($context);
    }
}
