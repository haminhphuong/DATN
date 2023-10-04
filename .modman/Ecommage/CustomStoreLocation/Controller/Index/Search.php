<?php
namespace Ecommage\CustomStoreLocation\Controller\Index;

use Magento\Framework\App\Action\HttpPostActionInterface as HttpPostActionInterface;
use Magento\Framework\App\ViewInterface;
use Magento\Framework\Exception\LocalizedException;

class Search extends \Magento\Framework\App\Action\Action implements HttpPostActionInterface
{
    /**
     * @var ViewInterface
     */
    protected $_view;

    /**
     * @var \Magento\Framework\Controller\Result\JsonFactory
     */
    protected $resultJsonFactory;

    /**
     * Ajax constructor.
     *
     * @param \Magento\Framework\App\Action\Context            $context
     * @param \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory
    ) {
        parent::__construct($context);
        $this->resultJsonFactory = $resultJsonFactory;
    }
    /**
     * Default customer account page
     *
     * @return void
     */
    public function execute()
    {
        $this->_view->loadLayout();// @codingStandardsIgnoreLine
        try {
            $block = $this->_view->getLayout()->getBlock('amlocator_ajax_search');

            $response = [
                'errors' => false,
                'html' => $block->toHtml()
            ];
        } catch (LocalizedException $e) {
            $response = [
                'errors' => true,
                'message' => $e->getMessage(),
            ];
        } catch (\Exception $e) {
            $response = [
                'errors' => true,
                'message' => __('Invalid Data.'),
            ];
        }
        /** @var \Magento\Framework\Controller\Result\Json $resultJson */
        $resultJson = $this->resultJsonFactory->create();
        return $resultJson->setData($response);
    }
}
