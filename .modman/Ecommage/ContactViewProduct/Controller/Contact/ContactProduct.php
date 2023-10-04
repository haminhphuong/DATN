<?php

namespace Ecommage\ContactViewProduct\Controller\Contact;

use Ecommage\ContactViewProduct\Model\ContactView;
use Ecommage\ContactViewProduct\Model\ContactViewFactory;
use Exception;
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\Controller\Result\Redirect;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\DataObject;
use Magento\Framework\Exception\InputException;
use Ecommage\BookingViewProduct\Helper\Data as HelperData;
use Magento\Catalog\Model\ProductRepository;

/**
 * Class Save
 * @package Ecommage\ContactViewProduct\Controller\Booking
 */
class ContactProduct extends Action
{
    /**
     * @var ContactViewFactory
     */
    protected $_contactViewFactory;
    /**
     * @var HelperData
     */
    protected $_helperData;
    /**
     * @var ProductRepository
     */
    protected $_productRepository;

    /**
     * @param Context $context
     * @param ContactViewFactory $contactViewFactory
     * @param HelperData $_helperData
     * @param ProductRepository $productRepository
     */
    public function __construct(
        Context $context,
        ContactViewFactory $contactViewFactory,
        HelperData $_helperData,
        ProductRepository $productRepository
    ) {
        parent::__construct($context);
        $this->_contactViewFactory = $contactViewFactory;
        $this->_helperData = $_helperData;
        $this->_productRepository = $productRepository;
    }

    /**
     * Booking action
     * @SuppressWarnings(PHPMD.StaticAccess)
     * @return Redirect|ResultInterface
     */
    public function execute()
    {
        try {
            $contactData = $this->getRequest()->getParams();
            $emailProductContact = $this->_helperData->getEmailProductContact();
            $templateId =  $this->_helperData->getEmailTemplateProductContact();
            /** @var ContactView $contact */
            $contact = $this->_contactViewFactory->create();
            $contactData['product_name'] = $this->_productRepository->getById($contactData['product_id'])->getName();
            $this->_helperData->sendEmail($contactData['email'], ['data' => new DataObject($contactData)],$templateId,$emailProductContact);
            $contact->setData($contactData)->save();
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
