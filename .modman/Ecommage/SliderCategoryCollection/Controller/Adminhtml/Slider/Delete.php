<?php

namespace Ecommage\SliderCategoryCollection\Controller\Adminhtml\Slider;

use Magento\Backend\App\Action;
use Psr\Log\LoggerInterface;
use Ecommage\SliderCategoryCollection\Model\ResourceModel\Slider\CollectionFactory;

/**
 * Class Delete
 *
 * @package Ecommage\SliderCategoryCollection\Controller\Adminhtml\Customer\Slider
 */
class Delete extends \Magento\Backend\App\Action
{
    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var CollectionFactory
     */
    private $collectionFactory;

    /**
     * Delete constructor.
     *
     * @param Action\Context    $context
     * @param LoggerInterface   $logger
     * @param CollectionFactory $collectionFactory
     */
    public function __construct(
        Action\Context $context,
        LoggerInterface $logger,
        CollectionFactory $collectionFactory
    )
    {
        parent::__construct($context);
        $this->logger            = $logger;
        $this->collectionFactory = $collectionFactory;
    }

    public function execute()
    {
        $id = $this->getRequest()->getParam('slider_ctg_id');

        try {
            $collection = $this->collectionFactory->create();
            $collection->addFieldToFilter('slider_ctg_id', ['eq' => $id]);
            $collection->walk('delete');
            $this->messageManager->addSuccessMessage(__('Slider(s) were successfully deleted'));
        } catch (\Exception $e) {
            $this->messageManager->addErrorMessage(
                __('We can\'t delete slider(s) right now. Please review the log and try again.') . $e->getMessage()
            );

            $this->logger->critical($e);
        }

        $this->_redirect('*/*/');
    }

    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Ecommage_SliderCategoryCollection::slider_category_collection');
    }
}
