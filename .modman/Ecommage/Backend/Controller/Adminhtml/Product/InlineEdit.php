<?php

namespace Ecommage\Backend\Controller\Adminhtml\Product;

use Exception;
use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Catalog\Model\ProductRepository;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\Result\Json;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Framework\Controller\ResultInterface;
use Ecommage\Backend\Model\ScheduleProductFactory;

class InlineEdit extends Action
{
    /**
     * @var JsonFactory
     */
    protected $jsonFactory;
    /**
     * @var ProductRepository
     */
    protected $productRepository;
    /**
     * @var ScheduleProductFactory
     */
    protected $scheduleProductFactory;

    /**
     * @param Context $context
     * @param JsonFactory $jsonFactory
     * @param ProductRepository $productRepository
     * @param ScheduleProductFactory $scheduleProductFactory
     */
    public function __construct(
        Context            $context,
        JsonFactory        $jsonFactory,
        ProductRepository $productRepository,
        ScheduleProductFactory $scheduleProductFactory
    )
    {
        parent::__construct($context);
        $this->jsonFactory = $jsonFactory;
        $this->productRepository = $productRepository;
        $this->scheduleProductFactory = $scheduleProductFactory;
    }

    /**
     * @return ResponseInterface|Json|ResultInterface
     * @SuppressWarnings(PHPMD.ElseExpression)
     */
    public function execute()
    {
        $resultJson = $this->jsonFactory->create();
        $error = false;
        $messages = [];
        if ($this->getRequest()->getParam('isAjax')) {
            $postItems = $this->getRequest()->getParam('items', []);
            if (empty($postItems)) {
                $messages[] = __('Please correct the data sent.');
                $error = true;
            } else {// @codingStandardsIgnoreLine
                foreach (array_keys($postItems) as $entityId) {
                    $scheduleProduct = $this->scheduleProductFactory->create()->load($entityId);
                    $storeId = $scheduleProduct->getStoreId();
                    $product = $this->productRepository->getById($scheduleProduct->getProductId());

                    try {
                        $scheduleProduct->setData(array_merge($scheduleProduct->getData(), $postItems[$entityId]));
                        $scheduleProduct->save();
                        $product->addAttributeUpdate('schedule_change_start',$postItems[$entityId]['schedule_date_start'],$storeId);
                        $product->addAttributeUpdate('schedule_change_end',$postItems[$entityId]['schedule_date_end'],$storeId);
                    } catch (Exception $e) {
                        $messages[] = "[Error:]  {$e->getMessage()}";
                        $error = true;
                    }
                }
            }
        }

        return $resultJson->setData([
            'messages' => $messages,
            'error' => $error
        ]);
    }
}
