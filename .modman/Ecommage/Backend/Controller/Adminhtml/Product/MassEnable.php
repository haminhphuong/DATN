<?php

namespace Ecommage\Backend\Controller\Adminhtml\Product;

use Magento\Ui\Component\MassAction\Filter;
use Ecommage\Backend\Model\ResourceModel\ScheduleProduct\CollectionFactory;
use Ecommage\Backend\Model\ResourceModel\ScheduleProduct;
use Psr\Log\LoggerInterface;
use Magento\Backend\App\Action;

class MassEnable extends Action
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
     * @var Filter
     */
    private $filter;

    /**
     * @var ScheduleProduct
     */
    private $scheduleProduct;

    /**
     * @param Action\Context $context
     * @param LoggerInterface $logger
     * @param CollectionFactory $collectionFactory
     * @param Filter $filter
     * @param ScheduleProduct $scheduleProduct
     */
    public function __construct(
        Action\Context $context,
        LoggerInterface $logger,
        CollectionFactory $collectionFactory,
        Filter $filter,
        ScheduleProduct $scheduleProduct
    ) {
        parent::__construct($context);
        $this->logger            = $logger;
        $this->collectionFactory = $collectionFactory;
        $this->filter            = $filter;
        $this->scheduleProduct = $scheduleProduct;
    }

    /**
     * @return \Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\ResultInterface|void
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function execute()
    {
        $collection = $this->filter->getCollection($this->collectionFactory->create());
        $status     = $this->getRequest()->getParam('enable');

        try {
            foreach ($collection->getItems() as $item) {
                $item->setStatus($status);
                $this->scheduleProduct->save($item);
            }

            $message = __('Record(s) have been updated.');
            $this->messageManager->addSuccessMessage($message);
        } catch (\Exception $e) {
            $this->messageManager->addErrorMessage(
                __('We can\'t change status of schedule product(s) right now. Please review the log and try again.') . $e->getMessage()
            );
            $this->logger->critical($e);
        }

        $this->_redirect('*/*/');
    }
}
