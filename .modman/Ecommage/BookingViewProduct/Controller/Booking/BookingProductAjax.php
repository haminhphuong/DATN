<?php

namespace Ecommage\BookingViewProduct\Controller\Booking;


use Ecommage\BookingViewProduct\Model\BookingViewFactory;
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Framework\DataObject;
use Magento\Framework\Serialize\Serializer\Json;
use Ecommage\BookingViewProduct\Helper\Data as HelperData;

class BookingProductAjax extends Action
{
    /**
     * @var Json
     */
    protected $json;
    /**
     * @var JsonFactory
     */
    protected $resultJsonFactory;
    /**
     * @var BookingViewFactory
     */
    protected $_bookingViewFactory;
    /**
     * @var HelperData
     */
    protected $_data;

    /**
     * @param Context $context
     * @param Json $json
     * @param JsonFactory $resultJsonFactory
     * @param BookingViewFactory $bookingViewFactory
     */
    public function __construct(Context     $context,
                                Json        $json,
                                JsonFactory $resultJsonFactory,
                                BookingViewFactory $bookingViewFactory,
                                HelperData $data
    )
    {
        $this->json = $json;
        $this->resultJsonFactory = $resultJsonFactory;
        $this->_bookingViewFactory = $bookingViewFactory;
        $this->_data = $data;
        parent::__construct($context);
    }

    /**
     * @return \Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\Result\Json|\Magento\Framework\Controller\ResultInterface
     * @throws \Exception
     */
    public function execute()
    {
        $bookingData = $this->getRequest()->getParams();
        $bookingData['product_name'] = $this->_data->getProductName($bookingData['product_id']);
        $bookingData['location_name'] = $this->_data->getLocationName($bookingData['location_id']);
        $bookingData['booking_type'] = $this->_data->getBookingType(isset($bookingData['booking_type']) ?: 1);
        $emailBooking = $this->_data->getEmailBooking();
        $templateId =  $this->_data->getEmailTemplateBooking();
        $booking = $this->_bookingViewFactory->create();
        $this->_data->sendEmail($bookingData['email'], ['data' => new DataObject($bookingData)],$templateId,$emailBooking);
        $booking->setData($bookingData)->save();
        $resultJson = $this->resultJsonFactory->create();
        return $resultJson->setData($bookingData);
    }
}
