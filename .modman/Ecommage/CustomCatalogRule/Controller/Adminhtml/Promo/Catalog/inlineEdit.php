<?php

namespace Ecommage\CustomCatalogRule\Controller\Adminhtml\Promo\Catalog;

use Exception;
use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\Result\Json;
use Magento\Framework\Controller\Result\JsonFactory;
use Ecommage\BookingViewProduct\Model\BookingView;
use Magento\CatalogRule\Model\Rule;
use Magento\CatalogRule\Model\RuleFactory;
use Magento\Framework\Controller\ResultInterface;

class InlineEdit extends Action
{
    /**
     * @var JsonFactory
     */
    protected $jsonFactory;
    /**
     * @var RuleFactory
     */
    protected $ruleFactory;

    /**
     * @param Context $context
     * @param JsonFactory $jsonFactory
     * @param RuleFactory $ruleFactory
     */
    public function __construct(
        Context            $context,
        JsonFactory        $jsonFactory,
        RuleFactory $ruleFactory
    )
    {
        parent::__construct($context);
        $this->jsonFactory = $jsonFactory;
        $this->_ruleFactory = $ruleFactory;
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
                    /** @var Rule $modelData */
                    $modelData = $this->_ruleFactory->create()->load($entityId);
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
