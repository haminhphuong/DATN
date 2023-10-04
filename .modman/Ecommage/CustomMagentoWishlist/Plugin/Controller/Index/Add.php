<?php

namespace Ecommage\CustomMagentoWishlist\Plugin\Controller\Index;

use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Customer\Model\Session;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\App\Response\RedirectInterface;
use Magento\Framework\Controller\Result\Redirect;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Data\Form\FormKey\Validator;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Exception\NotFoundException;
use Magento\Framework\UrlInterface;
use Magento\Wishlist\Controller\WishlistProviderInterface;

class Add
{
    /**
     * @var WishlistProviderInterface
     */
    protected $wishlistProvider;

    /**
     * @var Session
     */
    protected $_customerSession;

    /**
     * @var ProductRepositoryInterface
     */
    protected $productRepository;

    /**
     * @var Validator
     */
    protected $formKeyValidator;

    /**
     * @var RedirectInterface
     */
    protected $redirect;

    /**
     * @var UrlInterface
     */
    protected $urlBuilder;
    /**
     * @var ResultFactory
     */
    protected $resultFactory;
    /**
     * @var \Magento\Framework\Message\ManagerInterface
     */
    protected $messageManager;
    /**
     * @var \Magento\Framework\Event\ManagerInterface
     */
    protected $_eventManager;
    /**
     * @var \Magento\Framework\ObjectManagerInterface
     */
    protected $_objectManager;
    /**
     * @var RedirectInterface
     */
    protected $_redirect;

    /**
     * @param Context $context
     * @param Session $customerSession
     * @param WishlistProviderInterface $wishlistProvider
     * @param ProductRepositoryInterface $productRepository
     * @param Validator $formKeyValidator
     * @param RedirectInterface|null $redirect
     * @param UrlInterface|null $urlBuilder
     */
    public function __construct(
        Context $context,
        Session $customerSession,
        WishlistProviderInterface $wishlistProvider,
        ProductRepositoryInterface $productRepository,
        Validator $formKeyValidator,
        RedirectInterface $redirect = null,
        UrlInterface $urlBuilder = null
    ) {
        $this->_customerSession = $customerSession;
        $this->wishlistProvider = $wishlistProvider;
        $this->productRepository = $productRepository;
        $this->formKeyValidator = $formKeyValidator;
        $this->redirect = $redirect ?: ObjectManager::getInstance()->get(RedirectInterface::class);
        $this->urlBuilder = $urlBuilder ?: ObjectManager::getInstance()->get(UrlInterface::class);
        $this->request = $context->getRequest();
        $this->resultFactory = $context->getResultFactory();
        $this->messageManager = $context->getMessageManager();
        $this->_eventManager = $context->getEventManager();
        $this->_objectManager = $context->getObjectManager();
        $this->_redirect = $context->getRedirect();
    }

    /**
     * @param \Magento\Wishlist\Controller\Index\Add $subject
     * @param callable $proceed
     * @return Json|Redirect
     * @throws NotFoundException
     */
    public function aroundExecute(\Magento\Wishlist\Controller\Index\Add $subject, callable $proceed){

        /** @var Redirect $resultRedirect */
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        if (!$this->formKeyValidator->validate($this->request)) {
            return $resultRedirect->setPath('*/');
        }

        $wishlist = $this->wishlistProvider->getWishlist();
        if (!$wishlist) {
            throw new NotFoundException(__('Page not found.'));
        }

        $session = $this->_customerSession;

        $requestParams = $this->request->getParams();

        if ($session->getBeforeWishlistRequest()) {
            $requestParams = $session->getBeforeWishlistRequest();
            $session->unsBeforeWishlistRequest();
        }

        $productId = isset($requestParams['product']) ? (int)$requestParams['product'] : null;
        if (!$productId) {
            $resultRedirect->setPath('*/');
            return $resultRedirect;
        }

        try {
            $product = $this->productRepository->getById($productId);
        } catch (NoSuchEntityException $e) {
            $product = null;
        }

        if (!$product || !$product->isVisibleInCatalog()) {
            $this->messageManager->addErrorMessage(__('We can\'t specify a product.'));
            $resultRedirect->setPath('*/');
            return $resultRedirect;
        }

        try {
            $buyRequest = new \Magento\Framework\DataObject($requestParams);

            $result = $wishlist->addNewItem($product, $buyRequest);
            if (is_string($result)) {
                throw new LocalizedException(__($result));
            }

            if ($wishlist->isObjectNew()) {
                $wishlist->save();
            }
            $this->_eventManager->dispatch(
                'wishlist_add_product',
                ['wishlist' => $wishlist, 'product' => $product, 'item' => $result]
            );

            $referer = $session->getBeforeWishlistUrl();
            if ($referer) {
                $session->setBeforeWishlistUrl(null);
            } else {
                $referer = $this->_redirect->getRefererUrl();
            }

            $this->_objectManager->get(\Magento\Wishlist\Helper\Data::class)->calculate();

            $this->messageManager->addComplexSuccessMessage(
                'amAjaxSuccessAddToWishlist',
                [
                    'product_name' => $product->getName(),
                    'wishlist_url' => $this->getWishListUrl()
                ]
            );
            // phpcs:disable Magento2.Exceptions.ThrowCatch
        } catch (LocalizedException $e) {
            $this->messageManager->addErrorMessage(
                __('We can\'t add the item to Wish List right now: %1.', $e->getMessage())
            );
        } catch (\Exception $e) {
            $this->messageManager->addExceptionMessage(
                $e,
                __('We can\'t add the item to Wish List right now: %1', $e->getMessage())
            );
        }

        if ($this->request->isAjax()) {
            $url = $this->urlBuilder->getUrl('*', $this->redirect->updatePathParams(['wishlist_id' => $wishlist->getId()]));

            /** @var Json $resultJson */
            $resultJson = $this->resultFactory->create(ResultFactory::TYPE_JSON);
            $resultJson->setData(['backUrl' => $url]);

            return $resultJson;
        }
        $resultRedirect->setPath('*', ['wishlist_id' => $wishlist->getId()]);
        return $resultRedirect->setRefererOrBaseUrl();
    }

    /**
     * Returns wishlist url
     *
     * @return string
     */
    public function getWishListUrl(): string
    {
        return $this->urlBuilder->getUrl('wishlist/index/index', ['_secure' => true]);
    }
}
