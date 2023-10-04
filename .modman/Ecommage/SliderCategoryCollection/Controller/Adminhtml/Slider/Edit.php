<?php

namespace Ecommage\SliderCategoryCollection\Controller\Adminhtml\Slider;

use Magento\Backend\App\Action;

/**
 * Class Edit
 *
 * @package Ecommage\SliderCategoryCollection\Controller\Adminhtml\Customer\Slider
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
     * @var \Ecommage\SliderCategoryCollection\Model\SliderFactory
     */
    private $sliderFactory;
    private $sliderResource;

    /**
     * Edit constructor.
     *
     * @param \Magento\Backend\App\Action\Context          $context
     * @param \Magento\Framework\Registry                  $registry
     * @param \Magento\Framework\View\Result\PageFactory   $resultPageFactory
     * @param \Ecommage\SliderCategoryCollection\Model\SliderFactory $sliderFactory
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Ecommage\SliderCategoryCollection\Model\SliderFactory $sliderFactory,
        \Ecommage\SliderCategoryCollection\Model\ResourceModel\Slider $sliderResource
    ) {
        $this->resultPageFactory = $resultPageFactory;
        $this->_coreRegistry     = $registry;
        $this->sliderFactory  = $sliderFactory;
        $this->sliderResource  = $sliderResource;
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
        $resultPage->setActiveMenu('Ecommage_SliderCategoryCollection::slider_category_collection')
                   ->addBreadcrumb(__('Manage Category Slider'), __('Manage Category Slider'));
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
        $id = $this->getRequest()->getParam('slider_ctg_id');
        $model= $this->sliderFactory->create();
        if($id){
            $this->sliderResource->load($model, $id);
            if(!$model->getId()){
                return $this->_redirect('slider_category_collection/slider/index');
            }
        }
        $this->_coreRegistry->register('ecommage_category_slider', $model);
        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        $resultPage = $this->_initAction();

        $resultPage->addBreadcrumb(
            $id ? __('Edit Category Slider') : __('New Category Slider'),
            $id ? __('Edit Category Slider') : __('New Category Slider')
        );

        $resultPage->getConfig()->getTitle()->prepend(__('Category Slider'));

        return $resultPage;
    }
}
