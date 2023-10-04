<?php

namespace Ecommage\SliderCollection\Controller\Adminhtml\Slider;

use Magento\Backend\App\Action;

/**
 * Class Index
 *
 * @package Ecommage\SliderCollection\Controller\Adminhtml\Customer\Slider
 */
class Index extends Action
{
    /**
     * Authorization level of a basic admin session
     *
     * @see _isAllowed()
     */
    const ADMIN_RESOURCE = 'Ecommage_SliderCollection::slider_collection';

    /**
     * @return \Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\ResultInterface|void
     */
    public function execute()
    {
        $this->initAction();
        $this->_view->getPage()->getConfig()->getTitle()->prepend(__('Slider'));
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
             ->_addBreadcrumb(__('Slider'), __('Slider'));

        return $this;
    }
}
