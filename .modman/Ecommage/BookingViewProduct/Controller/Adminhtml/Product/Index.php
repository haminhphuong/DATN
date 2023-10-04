<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_GroupAssign
 */

namespace Ecommage\BookingViewProduct\Controller\Adminhtml\Product;

use Magento\Framework\Controller\ResultFactory;

/**
 * Class Index
 * @package Ecommage\BookingViewProduct\Controller\Adminhtml\Product
 */
class Index extends \Magento\Backend\App\Action
{
    /**
     * Index action
     *
     * @return \Magento\Backend\Model\View\Result\Page
     */
    public function execute()
    {
        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        $resultPage = $this->resultFactory->create(ResultFactory::TYPE_PAGE);
        $resultPage->setActiveMenu('Ecommage_BookingViewProduct::manage_booking_product');
        $resultPage->getConfig()->getTitle()->prepend(__('Manage Booking Product'));
        $resultPage->addBreadcrumb(__('Manage Booking Product'), __('Manage Booking Product'));

        return $resultPage;
    }

    /**
     * Check if user has permissions to access this controller
     *
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed("Ecommage_BookingViewProduct::manage_booking_product");
    }
}
