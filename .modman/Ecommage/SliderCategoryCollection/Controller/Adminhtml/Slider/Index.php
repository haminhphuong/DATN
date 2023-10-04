<?php

namespace Ecommage\SliderCategoryCollection\Controller\Adminhtml\Slider;

use Magento\Backend\App\Action;

/**
 * Class Index
 *
 * @package Ecommage\SliderCategoryCollection\Controller\Adminhtml\Customer\Slider
 */
class Index extends Action
{
    /**
     * Authorization level of a basic admin session
     *
     * @see _isAllowed()
     */
    const ADMIN_RESOURCE = 'Ecommage_SliderCategoryCollection::slider_category_collection';

    /**
     * @return \Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\ResultInterface|void
     */
    public function execute()
    {
        $this->initAction();
        $this->_view->getPage()->getConfig()->getTitle()->prepend(__('Category Slider'));
        $this->_view->renderLayout();
    }

    /**
     * Initiate action
     *
     * @return $this
     */
    private function initAction()
    {
        $this->_view->loadLayout();
        $this->_setActiveMenu(self::ADMIN_RESOURCE)
             ->_addBreadcrumb(__('Category Slider'), __('Category Slider'));

        return $this;
    }
}
