<?php
namespace Ecommage\CustomCheckout\Plugin\Controller\Index;

use Amasty\CheckoutCore\Model\Config;
use Magento\Checkout\Helper\Data;
use Magento\Checkout\Model\Session as CheckoutSession;
use Magento\Customer\Model\Session;
use Magento\Framework\App\Action\Context;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\View\Result\Page;
use Magento\Framework\View\Result\PageFactory;
use Amasty\CheckoutCore\Controller\Index\Index;
use Zend\Uri\UriFactory;

class CheckoutOOS
{
    /**
     * @var Data
     */
    protected $checkoutHelper;

    /**
     * @var Config
     */
    protected $amCheckoutConfig;

    /**
     * @var CheckoutSession
     */
    protected $checkoutSession;
    /**
     * @var PageFactory
     */
    protected $resultPageFactory;
    /**
     * @var \Magento\Framework\App\RequestInterface
     */
    protected $request;
    /**
     * @var Session
     */
    protected $customerSession;

    /**
     * @param Context $context
     * @param Session $customerSession
     * @param PageFactory $resultPageFactory
     * @param Config $amCheckoutConfig
     * @param CheckoutSession $checkoutSession
     */
    public function __construct(
        Context $context,
        Session $customerSession,
        PageFactory $resultPageFactory,
        Config $amCheckoutConfig,
        CheckoutSession $checkoutSession
    ) {
        $this->request = $context->getRequest();
        $this->customerSession = $customerSession;
        $this->resultPageFactory = $resultPageFactory;
        $this->amCheckoutConfig = $amCheckoutConfig;
        $this->checkoutSession = $checkoutSession;
    }


    /**
     * Checkout page
     *
     * @return ResultInterface|Page
     */
    public function afterExecute(Index $subject, $result)
    {
        if (!$this->isSecureRequest()) {
            $this->customerSession->regenerateId();
        }
        $this->checkoutSession->setCartWasUpdated(false);
        $subject->getOnepage()->initCheckout();

        $resultPage = $this->prepareResultPage();
        $resultPage->getConfig()->getTitle()->set(__('Checkout'));

        $quote = $subject->getOnepage()->getQuote();

        if (!$quote->hasItems() || $quote->getHasError() || !$quote->validateMinimumAmount()) {
            return $resultPage;
        }
        return $result;

    }

    /**
     * @return Page
     */
    protected function prepareResultPage()
    {
        $resultPage = $this->resultPageFactory->create();

        if ($font = $this->amCheckoutConfig->getCustomFont()) {
            $resultPage->getConfig()->addRemotePageAsset(
                'https://fonts.googleapis.com/css?family=' . urlencode($font),
                'css'
            );
        }

        $resultPage->getLayout()->getUpdate()->addHandle('amasty_checkout');

        if ($this->amCheckoutConfig->getHeaderFooter()) {
            $resultPage->getLayout()->getUpdate()->addHandle('amasty_checkout_headerfooter');
        }

        if ($this->amCheckoutConfig->isCheckoutItemsEditable()) {
            $resultPage->getLayout()->getUpdate()->addHandle('amasty_checkout_prototypes');
        }

        return $resultPage;
    }

    /**
     * Checks if current request uses SSL and referer also is secure.
     *
     * @return bool
     */
    protected function isSecureRequest(): bool
    {

        $referrer = $this->request->getHeader('referer');
        $secure = false;

        if ($referrer) {
            $scheme = UriFactory::factory($referrer)->getScheme();
            $secure = $scheme === 'https';
        }

        return $secure && $this->request->isSecure();
    }
}
