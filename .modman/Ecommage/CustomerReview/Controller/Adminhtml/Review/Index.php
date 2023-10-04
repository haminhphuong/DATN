<?php

namespace Ecommage\CustomerReview\Controller\Adminhtml\Review;

use Magento\Backend\App\Action;

/**
 * Class Index
 *
 * @package Ecommage\CustomerReview\Controller\Adminhtml\Customer\Review
 */
class Index extends Action
{
    /**
     * Authorization level of a basic admin session
     *
     * @see _isAllowed()
     */
    const ADMIN_RESOURCE = 'Ecommage_CustomerReview::customer_review';

    /**
     * @return \Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\ResultInterface|void
     */
    public function execute()
    {
        $this->initAction();
        $this->_view->getPage()->getConfig()->getTitle()->prepend(__('Review'));
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
             ->_addBreadcrumb(__('Review'), __('Review'));

        return $this;
    }
}
