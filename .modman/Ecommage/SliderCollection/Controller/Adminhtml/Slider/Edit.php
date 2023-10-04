<?php

namespace Ecommage\SliderCollection\Controller\Adminhtml\Slider;

use Magento\Backend\App\Action;

/**
 * Class Edit
 *
 * @package Ecommage\SliderCollection\Controller\Adminhtml\Customer\Slider
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
     * @var \Ecommage\SliderCollection\Model\SliderFactory
     */
    private $sliderFactory;
    private $sliderResource;

    /**
     * Edit constructor.
     *
     * @param \Magento\Backend\App\Action\Context          $context
     * @param \Magento\Framework\Registry                  $registry
     * @param \Magento\Framework\View\Result\PageFactory   $resultPageFactory
     * @param \Ecommage\SliderCollection\Model\SliderFactory $sliderFactory
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Ecommage\SliderCollection\Model\SliderFactory $sliderFactory,
        \Ecommage\SliderCollection\Model\ResourceModel\Slider $sliderResource
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
        $resultPage->setActiveMenu('Ecommage_SliderCollection::slider_collection')
                   ->addBreadcrumb(__('Manage Slider'), __('Manage Slider'));
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
        $id = $this->getRequest()->getParam('slider_id');
        $model= $this->sliderFactory->create();
        if($id){
            $this->sliderResource->load($model, $id);
            if(!$model->getId()){
                return $this->_redirect('slider_collection/slider/index');
            }
        }
        $this->_coreRegistry->register('ecommage_slider', $model);
        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        $resultPage = $this->_initAction();

        $resultPage->addBreadcrumb(
            $id ? __('Edit Slider') : __('New Slider'),
            $id ? __('Edit Slider') : __('New Slider')
        );

        $resultPage->getConfig()->getTitle()->prepend(__('Slider'));

        return $resultPage;
    }
}
