<?php

namespace Ecommage\ContactViewProduct\Controller\Adminhtml\Product;

use Exception;
use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\Result\Json;
use Magento\Framework\Controller\Result\JsonFactory;
use Ecommage\ContactViewProduct\Model\ContactView;
use Ecommage\ContactViewProduct\Model\ContactViewFactory;
use Magento\Framework\Controller\ResultInterface;

class InlineEdit extends Action
{
    /**
     * @var JsonFactory
     */
    protected $jsonFactory;
    /**
     * @var ContactViewFactory
     */
    protected $contactViewFactory;

    /**
     * @param Context $context
     * @param JsonFactory $jsonFactory
     * @param ContactViewFactory $contactViewFactory
     */
    public function __construct(
        Context            $context,
        JsonFactory        $jsonFactory,
        ContactViewFactory $contactViewFactory
    )
    {
        parent::__construct($context);
        $this->jsonFactory = $jsonFactory;
        $this->contactViewFactory = $contactViewFactory;
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
                    /** @var ContactView $modelData */
                    $modelData = $this->contactViewFactory->create()->load($entityId);
                    try {
                        $modelData->setData(array_merge($modelData->getData(), $postItems[$entityId]));
                        $modelData->save();
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
