<?php

namespace Ecommage\CustomSales\Plugin\Magento\Sales\Controller\AbstractController;
use Magento\Checkout\Model\Session as CheckoutSession;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\ObjectManagerInterface;
use Magento\Framework\Registry;
use Magento\Sales\Controller\AbstractController\OrderLoaderInterface;
use Magento\Sales\Helper\Reorder as ReorderHelper;

class ReorderPlugin
{
    /**
     * @var \Magento\Sales\Controller\AbstractController\OrderLoaderInterface
     */
    protected $orderLoader;

    /**
     * @var Registry
     */
    protected $_coreRegistry;

    /**
     * @var \Magento\Sales\Model\Reorder\Reorder
     */
    private $reorder;

    /**
     * @var CheckoutSession
     */
    private $checkoutSession;
    /**
     * @var \Magento\Framework\Controller\Result\RedirectFactory
     */
    protected $resultRedirectFactory;
    /**
     * @var \Magento\Framework\UrlInterface
     */
    protected $_url;
    /**
     * @var \Magento\Framework\Message\ManagerInterface
     */
    protected $messageManager;
    /**
     *
     * @var ObjectManagerInterface $_objectManager
     */

    protected $_objectManager;
    /**
     * @var Magento\Framework\App\RequestInterface
     */
    protected $request;

    /**
     * Constructor
     *
     * @param \Magento\Framework\App\Action\Context $context
     * @param OrderLoaderInterface $orderLoader
     * @param Registry $registry
     * @param ReorderHelper|null $reorderHelper
     * @param \Magento\Sales\Model\Reorder\Reorder|null $reorder
     * @param CheckoutSession|null $checkoutSession
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        OrderLoaderInterface $orderLoader,
        Registry $registry,
        ReorderHelper $reorderHelper = null,
        \Magento\Sales\Model\Reorder\Reorder $reorder = null,
        CheckoutSession $checkoutSession = null
    )
    {
        $this->orderLoader = $orderLoader;
        $this->_coreRegistry = $registry;
        $this->resultRedirectFactory = $context->getResultRedirectFactory();
        $this->_url = $context->getUrl();
        $this->messageManager = $context->getMessageManager();
        $this->request=$context->getRequest();
        $this->reorder = $reorder ?: ObjectManager::getInstance()->get(\Magento\Sales\Model\Reorder\Reorder::class);
        $this->checkoutSession = $checkoutSession ?: ObjectManager::getInstance()->get(CheckoutSession::class);

    }

    public function aroundExecute(
        \Magento\Sales\Controller\AbstractController\Reorder $subject ,
        callable $process)
    {
        $result = $this->orderLoader->load($this->request);
        if ($result instanceof \Magento\Framework\Controller\ResultInterface) {
            return $result;
        }
        $order = $this->_coreRegistry->registry('current_order');

        /** @var \Magento\Framework\Controller\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();

        try {
            $reorderOutput = $this->reorder->execute($order->getIncrementId(), $order->getStoreId());
        } catch (LocalizedException $localizedException) {
            $this->messageManager->addErrorMessage($localizedException->getMessage());
            return $resultRedirect->setPath('checkout');
        }

        // Set quote id for guest session: \Magento\Quote\Api\CartRepositoryInterface::save doesn't set quote id
        // to session for guest customer, as it does \Magento\Checkout\Model\Cart::save which is deprecated.
        $this->checkoutSession->setQuoteId($reorderOutput->getCart()->getId());

        $errors = $reorderOutput->getErrors();
        if (!empty($errors)) {
            $useNotice = $this->_objectManager->get(\Magento\Checkout\Model\Session::class)->getUseNotice(true);
            foreach ($errors as $error) {
                $useNotice
                    ? $this->messageManager->addNoticeMessage($error->getMessage())
                    : $this->messageManager->addErrorMessage($error->getMessage());
            }
        }

        return $resultRedirect->setPath('checkout');
    }



}
