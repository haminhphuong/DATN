<?php

namespace Ecommage\BannerManager\Controller\Adminhtml\Item;

use Magento\Backend\App\Action\Context;
use Magento\Framework\App\Action\HttpPostActionInterface;
use Magento\Framework\App\Request\DataPersistorInterface;
use Magento\Framework\Controller\Result\Json;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Framework\Registry;
use Magento\Framework\View\Result\PageFactory;
use Psr\Log\LoggerInterface;
use Ecommage\BannerManager\Model\Banner\ItemFactory;

/**
 * Class Delete
 */
class Delete extends BannerItem implements HttpPostActionInterface
{
    /**
     * @var JsonFactory
     */
    private $resultJsonFactory;
    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * Delete constructor.
     *
     * @param Context                $context
     * @param ItemFactory            $itemFactory
     * @param DataPersistorInterface $dataPersistor
     * @param PageFactory            $resultPageFactory
     * @param JsonFactory            $resultJsonFactory
     * @param LoggerInterface        $logger
     */
    public function __construct(
        Context $context,
        ItemFactory $itemFactory,
        DataPersistorInterface $dataPersistor,
        PageFactory $resultPageFactory,
        JsonFactory $resultJsonFactory,
        LoggerInterface $logger
    ) {
        parent::__construct($context, $itemFactory, $dataPersistor, $resultPageFactory);
        $this->resultJsonFactory = $resultJsonFactory;
        $this->logger            = $logger;
    }

    /**
     * @return Json
     */
    public function execute(): Json
    {
        $error   = false;
        $message = __('Banner Item is no longer exist');
        $id      = $this->getRequest()->getParam('id');
        if ($id) {
            $model = $this->_itemFactory->create();
            $model->load($id);
            if ($model->getId()) {
                try {
                    $model->delete();
                    $message = __('You deleted the banner item.');
                } catch (\Exception $e) {
                    $error = true;
                    $this->logger->critical($e);
                    $message = __('We can\'t delete the address right now.');
                }
            }
        }

        $resultJson = $this->resultJsonFactory->create();
        $resultJson->setData(
            [
                'message' => $message,
                'error'   => $error,
            ]
        );

        return $resultJson;
    }
}
