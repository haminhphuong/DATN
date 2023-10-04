<?php

namespace Ecommage\BookingViewProduct\Controller\Booking;

use Ecommage\BookingViewProduct\Helper\Data as HelperData;
use Ecommage\BookingViewProduct\Model\BookingView;
use Ecommage\BookingViewProduct\Model\BookingViewFactory;
use Exception;
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\Controller\Result\Redirect;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\DataObject;
use Magento\Framework\Exception\InputException;
use Magento\Catalog\Model\ProductRepository;
use Amasty\Storelocator\Model\LocationFactory;

/**
 * Class Save
 * @package Ecommage\BookingViewProduct\Controller\Booking
 */
class BookingProduct extends Action
{
    const DIRECTORY_DOCUMENT = 'catalog/product/attachment/';
    /**
     * @var HelperData
     */
    protected $_helperData;
    /**
     * @var BookingViewFactory
     */
    protected $_bookingViewFactory;
    /**
     * @var ProductRepository
     */
    protected $_productRepository;
    /**
     * @var LocationFactory
     */
    protected $_locationFactory;

    /**
     * @param Context $context
     * @param HelperData $helperData
     * @param BookingViewFactory $bookingViewFactory
     * @param ProductRepository $productRepository
     * @param LocationFactory $locationFactory
     */
    public function __construct(
        Context $context,
        HelperData $helperData,
        BookingViewFactory $bookingViewFactory,
        ProductRepository $productRepository,
        LocationFactory $locationFactory
    ) {
        parent::__construct($context);
        $this->_helperData = $helperData;
        $this->_bookingViewFactory = $bookingViewFactory;
        $this->_productRepository = $productRepository;
        $this->_locationFactory = $locationFactory;
    }

    /**
     * Booking action
     * @SuppressWarnings(PHPMD.StaticAccess)
     * @return Redirect|ResultInterface
     */
    public function execute()
    {
        try {
            $bookingData = $this->getRequest()->getParams();
            $emailBooking = $this->_helperData->getEmailBooking();
            $templateId =  $this->_helperData->getEmailTemplateBooking();

            /** @var BookingView $booking */
            $booking = $this->_bookingViewFactory->create();

            $bookingData['product_name'] = $this->_helperData->getProductName($bookingData['product_id']);
            $bookingData['location_name'] = $this->_helperData->getLocationName($bookingData['location_id']);
            $bookingData['booking_type'] = $this->_helperData->getBookingType($bookingData['booking_type']);

            $this->_helperData->sendEmail($bookingData['email'], ['data' => new DataObject($bookingData)],$templateId,$emailBooking);
            $booking->setData($bookingData)->save();

            $this->messageManager->addSuccessMessage(__('The system has received your information.'));
        } catch (InputException $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
            foreach ($e->getErrors() as $error) {
                $this->messageManager->addErrorMessage($error->getMessage());
            }
        } catch (Exception $e) {
            $this->messageManager->addExceptionMessage($e, __('We can\'t received your information!'));
        }
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        $resultRedirect->setUrl($this->_redirect->getRefererUrl());// @codingStandardsIgnoreLine
        return $resultRedirect;
    }

}
