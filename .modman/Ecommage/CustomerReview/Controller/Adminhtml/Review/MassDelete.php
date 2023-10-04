<?php

namespace Ecommage\CustomerReview\Controller\Adminhtml\Review;

use Magento\Backend\App\Action;
use Psr\Log\LoggerInterface;
use Ecommage\CustomerReview\Model\ResourceModel\Review\CollectionFactory;
use Magento\Ui\Component\MassAction\Filter;
use Ecommage\CustomerReview\Model\ResourceModel\Review;

/**
 * Class MassDelete
 *
 * @package Ecommage\CustomerReview\Controller\Adminhtml\Customer\Review
 */
class MassDelete extends \Magento\Backend\App\Action
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
     * @var Review
     */
    private $reviewResource;

    /**
     * MassDelete constructor.
     *
     * @param Action\Context    $context
     * @param LoggerInterface   $logger
     * @param CollectionFactory $collectionFactory
     * @param Filter            $filter
     * @param Review            $reviewResource
     */
    public function __construct(
        Action\Context $context,
        LoggerInterface $logger,
        CollectionFactory $collectionFactory,
        Filter $filter,
        Review $reviewResource
    ) {
        parent::__construct($context);
        $this->logger = $logger;
        $this->collectionFactory = $collectionFactory;
        $this->filter = $filter;
        $this->reviewResource = $reviewResource;
    }

    public function execute()
    {
        $collection = $this->filter->getCollection($this->collectionFactory->create());

        try {
            foreach ($collection->getItems() as $item) {
                $this->reviewResource->delete($item);
            }
            $this->messageManager->addSuccessMessage(__('Review(s) were successfully deleted'));
        } catch (\Exception $e) {
            $this->messageManager->addErrorMessage(
                __('We can\'t delete review(s) right now. Please review the log and try again. ') . $e->getMessage()
            );

            $this->logger->critical($e);
        }

        $this->_redirect('*/*/');
    }

    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Ecommage_CustomerReview::customer_review');
    }
}
