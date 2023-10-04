<?php

namespace Ecommage\CustomerReview\Controller\Adminhtml\Review;

use Magento\Backend\App\Action;

/**
 * Class Edit
 *
 * @package Ecommage\CustomerReview\Controller\Adminhtml\Customer\Review
 */
class Edit extends Action
{
    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry = null;

    /**
     * @var \Magento\Framework\View\Result\PageFactory
     */
    protected $resultPageFactory;

    /**
     * @var \Ecommage\CustomerReview\Model\ReviewFactory
     */
    private $reviewFactory;
    private $reviewResource;

    /**
     * Edit constructor.
     *
     * @param \Magento\Backend\App\Action\Context          $context
     * @param \Magento\Framework\Registry                  $registry
     * @param \Magento\Framework\View\Result\PageFactory   $resultPageFactory
     * @param \Ecommage\CustomerReview\Model\ReviewFactory $reviewFactory
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Ecommage\CustomerReview\Model\ReviewFactory $reviewFactory,
        \Ecommage\CustomerReview\Model\ResourceModel\Review $reviewResource
    ) {
        $this->resultPageFactory = $resultPageFactory;
        $this->_coreRegistry     = $registry;
        $this->reviewFactory  = $reviewFactory;
        $this->reviewResource  = $reviewResource;
        parent::__construct($context);
    }

    /**
     * Init actions
     *
     * @return \Magento\Backend\Model\View\Result\Page
     */
    protected function _initAction()
    {
        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        $resultPage = $this->resultPageFactory->create();
        $resultPage->setActiveMenu('Ecommage_CustomerReview::customer_review')
                   ->addBreadcrumb(__('Manage Customer Review'), __('Manage Customer Review'));
        return $resultPage;
    }

    /**
     * Edit CMS page
     *
     * @return \Magento\Backend\Model\View\Result\Page|\Magento\Backend\Model\View\Result\Redirect|\Magento\Framework\App\ResponseInterface
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    public function execute()
    {
        $id = $this->getRequest()->getParam('review_id');
        $model= $this->reviewFactory->create();
        if($id){
            $this->reviewResource->load($model, $id);
            if(!$model->getId()){
                return $this->_redirect('customer_review/review/index');// @codingStandardsIgnoreLine
            }
        }
        $this->_coreRegistry->register('ecommage_customer_review', $model);
        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        $resultPage = $this->_initAction();

        $resultPage->addBreadcrumb(
            $id ? __('Edit Review') : __('New Review'),
            $id ? __('Edit Review') : __('New Review')
        );

        $resultPage->getConfig()->getTitle()->prepend(__('Review'));

        return $resultPage;
    }
}
