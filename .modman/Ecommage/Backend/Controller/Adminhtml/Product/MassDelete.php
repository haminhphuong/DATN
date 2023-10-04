<?php

namespace Ecommage\Backend\Controller\Adminhtml\Product;

use Magento\Backend\App\Action;
use Psr\Log\LoggerInterface;
use Ecommage\Backend\Model\ResourceModel\ScheduleProduct\CollectionFactory;
use Magento\Ui\Component\MassAction\Filter;
use Ecommage\Backend\Model\ResourceModel\ScheduleProduct;
use Magento\Framework\Filesystem;
use Magento\Framework\App\Filesystem\DirectoryList;

class MassDelete extends Action
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

    private $directory;
    /**
     * @var Filesystem
     */
    private $filesystem;

    /**
     * @param Action\Context $context
     * @param LoggerInterface $logger
     * @param CollectionFactory $collectionFactory
     * @param Filter $filter
     * @param ScheduleProduct $scheduleProduct
     * @param Filesystem $filesystem
     */
    public function __construct(
        Action\Context $context,
        LoggerInterface $logger,
        CollectionFactory $collectionFactory,
        Filter $filter,
        ScheduleProduct $scheduleProduct,
        Filesystem $filesystem
    ) {
        parent::__construct($context);
        $this->logger = $logger;
        $this->collectionFactory = $collectionFactory;
        $this->filter = $filter;
        $this->scheduleProduct = $scheduleProduct;
        $this->filesystem = $filesystem;
    }

    public function execute()
    {
        $collection = $this->filter->getCollection($this->collectionFactory->create());
        $this->directory = $this->filesystem->getDirectoryWrite(DirectoryList::VAR_DIR);
        try {
            foreach ($collection->getItems() as $item) {
                $this->scheduleProduct->delete($item);
                $filepath = 'schedule/schedule_product_'.$item->getScheduleId().'_'.$item->getProductId() . '.csv';
                $this->directory->delete($filepath);
            }
            $this->messageManager->addSuccessMessage(__('Schedule product(s) were successfully deleted'));
        } catch (\Exception $e) {
            $this->messageManager->addErrorMessage(
                __('We can\'t delete schedule product(s) right now. Please review the log and try again. ') . $e->getMessage()
            );

            $this->logger->critical($e);
        }

        $this->_redirect('*/*/');
    }
}
