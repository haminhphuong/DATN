<?php

namespace Ecommage\BookingViewProduct\Helper;

use Amasty\Storelocator\Model\Location;
use Amasty\Storelocator\Model\LocationFactory;
use Amasty\Storelocator\Model\ResourceModel\Location\CollectionFactory;
use Magento\Catalog\Model\Product;
use Magento\Catalog\Model\ProductFactory;
use Magento\Catalog\Model\ProductRepository;
use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\Registry;
use Magento\Contact\Model\ConfigInterface;
use Magento\Framework\App\Area;
use Magento\Framework\Translate\Inline\StateInterface;
use Magento\Framework\Mail\Template\TransportBuilder;
use Magento\Store\Model\ScopeInterface;
use Magento\Store\Model\StoreManagerInterface;
/**
 * Class Data
 * @package Ecommage\BookingViewProduct\Helper
 */
class Data extends AbstractHelper
{
    const PATH_EMAIL_BOOKING = 'contact/custom_email_booking/email_booking';
    const PATH_EMAIL_TEMPLATE_BOOKING = 'contact/custom_email_booking/email_template';
    const PATH_EMAIL_PRODUCT_CONTACT = 'contact/custom_email_contact/email_product_contact';
    const PATH_EMAIL_TEMPLATE_PRODUCT_CONTACT = 'contact/custom_email_contact/email_template';

    /**
     * @var Registry
     */
    protected $_registry;
    /**
     * @var ProductFactory
     */
    protected $_productFactory;
    /**
     * @var CollectionFactory
     */
    protected CollectionFactory $_locationCollectionFactory;

    /**
     * @var StateInterface
     */
    protected $inlineTranslation;
    /**
     * @var TransportBuilder
     */
    protected $transportBuilder;
    /**
     * @var \Psr\Log\LoggerInterface
     */
    protected $logger;
    /**
     * @var ConfigInterface
     */
    protected $contactsConfig;
    /**
     * @var StoreManagerInterface|mixed
     */
    protected $storeManager;
    /**
     * @var ProductRepository
     */
    protected $productRepository;
    /**
     * @var LocationFactory
     */
    protected $locationFactory;

    /**
     * @param Context $context
     * @param Registry $registry
     * @param ProductFactory $productFactory
     * @param CollectionFactory $locationCollectionFactory
     * @param StateInterface $inlineTranslation
     * @param TransportBuilder $transportBuilder
     * @param ConfigInterface $contactsConfig
     * @param StoreManagerInterface|null $storeManager
     */
    public function __construct(
        Context                               $context,
        Registry                              $registry,
        ProductFactory $productFactory,
        CollectionFactory                     $locationCollectionFactory,
        StateInterface $inlineTranslation,
        TransportBuilder $transportBuilder,
        ConfigInterface $contactsConfig,
        ProductRepository $productRepository,
        LocationFactory $locationFactory,
        StoreManagerInterface $storeManager = null
    )
    {
        $this->_registry = $registry;
        $this->_productFactory = $productFactory;
        $this->_locationCollectionFactory = $locationCollectionFactory;
        $this->inlineTranslation = $inlineTranslation;
        $this->transportBuilder = $transportBuilder;
        $this->logger = $context->getLogger();
        $this->contactsConfig = $contactsConfig;
        $this->productRepository = $productRepository;
        $this->locationFactory = $locationFactory;
        $this->storeManager = $storeManager ?: ObjectManager::getInstance()->get(StoreManagerInterface::class);
        parent::__construct($context);
    }

    /**
     * @return array
     * @SuppressWarnings(PHPMD.BooleanArgumentFlag)
     */
    public function getLocationsData()
    {
        $locationDatas = [];
        $collection = $this->_locationCollectionFactory->create();
        foreach ($collection as $_location) {
            /** @var $_location Location */
            $locationDatas[] = [
                'value' => $_location->getId(),
                'label' => (string)__($_location->getName()),
            ];
        }

        return $locationDatas;
    }

    /**
     * @return mixed|null
     */
    public function getCurrentProduct()
    {
        return $this->_registry->registry('current_product');
    }

    /**
     * @param $productId
     * @return Product
     */
    public function getProductModel($productId)
    {
        return $this->_productFactory->create()
            ->load($productId);
    }

    /**
     * @param $replyTo
     * @param array $variables
     * @param $templateIdentifier
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\MailException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function sendEmail($replyTo, array $variables, $templateIdentifier, $emailRecipient = null, $name = null)
    {
        $emailRecipient = $emailRecipient != null ? $emailRecipient : $this->contactsConfig->emailRecipient();
        $name = $name != null ? $name : '';
        /** @see \Magento\Contact\Controller\Index\Post::validatedParams() */
        $replyToName = !empty($variables['data']['name']) ? $variables['data']['name'] : null;

        $this->inlineTranslation->suspend();
        try {
            $transport = $this->transportBuilder
                ->setTemplateIdentifier($templateIdentifier)
                ->setTemplateOptions(
                    [
                        'area' => Area::AREA_FRONTEND,
                        'store' => $this->storeManager->getStore()->getId()
                    ]
                )
                ->setTemplateVars($variables)
                ->setFrom($this->contactsConfig->emailSender())
                ->addTo($emailRecipient, $name)
                ->setReplyTo($replyTo, $replyToName)
                ->getTransport();

            $transport->sendMessage();
        } finally {
            $this->inlineTranslation->resume();
        }
    }

    /**
     * @param $path
     * @param null $storeId
     * @return mixed
     */
    public function getEmailBooking()
    {
        $storeId = $this->storeManager->getStore()->getId();
        return $this->scopeConfig->getValue(
            self::PATH_EMAIL_BOOKING,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    /**
     * @param $path
     * @param null $storeId
     * @return mixed
     */
    public function getEmailTemplateBooking()
    {
        $storeId = $this->storeManager->getStore()->getId();
        return $this->scopeConfig->getValue(
            self::PATH_EMAIL_TEMPLATE_BOOKING,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    /**
     * @param $path
     * @param null $storeId
     * @return mixed
     */
    public function getEmailProductContact()
    {
        $storeId = $this->storeManager->getStore()->getId();
        return $this->scopeConfig->getValue(
            self::PATH_EMAIL_PRODUCT_CONTACT,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    /**
     * @param $path
     * @param null $storeId
     * @return mixed
     */
    public function getEmailTemplateProductContact()
    {
        $storeId = $this->storeManager->getStore()->getId();
        return $this->scopeConfig->getValue(
            self::PATH_EMAIL_TEMPLATE_PRODUCT_CONTACT,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    /**
     * @param $locationId
     * @return string
     */
    public function getLocationName($locationId)
    {
        $locationModel = $this->locationFactory->create()
            ->load($locationId);
        return $locationModel->getName();
    }

    /**
     * @param $productId
     * @return string|null
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getProductName($productId){
        return $this->productRepository->getById($productId)->getName();
    }

    /**
     * @param $bookingType
     * @return string
     */
    public function getBookingType($bookingType){
        return $bookingType ? 'Online' : 'Offline';
    }
}
