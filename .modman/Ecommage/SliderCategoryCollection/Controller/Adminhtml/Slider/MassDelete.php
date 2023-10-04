<?php

namespace Ecommage\SliderCategoryCollection\Controller\Adminhtml\Slider;

use Magento\Backend\App\Action;
use Psr\Log\LoggerInterface;
use Ecommage\SliderCategoryCollection\Model\ResourceModel\Slider\CollectionFactory;
use Magento\Ui\Component\MassAction\Filter;
use Ecommage\SliderCategoryCollection\Model\ResourceModel\Slider;

/**
 * Class MassDelete
 *
 * @package Ecommage\SliderCategoryCollection\Controller\Adminhtml\Customer\Slider
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
     * @var Slider
     */
    private $sliderResource;

    /**
     * MassDelete constructor.
     *
     * @param Action\Context    $context
     * @param LoggerInterface   $logger
     * @param CollectionFactory $collectionFactory
     * @param Filter            $filter
     * @param Slider            $sliderResource
     */
    public function __construct(
        Action\Context $context,
        LoggerInterface $logger,
        CollectionFactory $collectionFactory,
        Filter $filter,
        Slider $sliderResource
    ) {
        parent::__construct($context);
        $this->logger = $logger;
        $this->collectionFactory = $collectionFactory;
        $this->filter = $filter;
        $this->sliderResource = $sliderResource;
    }

    public function execute()
    {
        $collection = $this->filter->getCollection($this->collectionFactory->create());

        try {
            foreach ($collection->getItems() as $item) {
                $this->sliderResource->delete($item);
            }
            $this->messageManager->addSuccessMessage(__('Category slider(s) were successfully deleted'));
        } catch (\Exception $e) {
            $this->messageManager->addErrorMessage(
                __('We can\'t delete category slider(s) right now. Please review the log and try again. ') . $e->getMessage()
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
