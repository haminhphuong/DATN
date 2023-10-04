<?php
namespace Ecommage\TabsPro\Controller\Ajax;

use Magento\Framework\View\Result\PageFactory;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Framework\Data\Form\FormKey;
use Ecommage\Recommendation\Helper\Data;
use Magezon\TabsPro\Model\TabFactory;
use Magento\Framework\App\Action\Context;

class Tab extends \Magezon\TabsPro\Controller\Ajax\Tab
{
    /**
     * @var Data
     */
    protected $helper;

    public function __construct
    (
        Context $context,
        PageFactory $resultPageFactory,
        JsonFactory $resultJsonFactory,
        TabFactory $tabFactory,
        FormKey $formKey,
        Data $helper
    )
    {
        $this->helper = $helper;
        parent::__construct($context, $resultPageFactory, $resultJsonFactory, $tabFactory, $formKey);
    }

    /**
     * Default customer account page
     *
     * @return \Magento\Framework\View\Result\Page
     */
    public function execute()
    {
        $response['status'] = false;
        $response = [];
        $post = $this->getRequest()->getPostValue();
        if ($post && isset($post['tab_id']) && $post['tab_id'] && isset($post['block_id']) && $post['block_id']) {
            try {
                $resultPage           = $this->resultPageFactory->create();
                $blockId              = $post['block_id'];
                $response['block_id'] = '#' . $blockId;
                $tabId                = $post['tab_id'];

                $formKey = $this->formKey->getFormKey();
                $block = $resultPage->getLayout()->createBlock('Magezon\TabsPro\Block\Tab')
                    ->setProductFormKey($formKey)
                    ->setTabProId($tabId)
                    ->setTabBlockId($blockId);

                $html = $this->helper->convertWebp($block->toHtml(),'cms_index_index');
                $response['status']   = true;
                $response['html']     = $html;
            } catch (\Exception $e) {
                $response['status'] = false;
            }
            /** @var \Magento\Framework\Controller\Result\Json $resultJson */
            $resultJson = $this->resultJsonFactory->create();
            return $resultJson->setData($response);
        } else {
            $resultRedirect = $this->resultRedirectFactory->create();
            $resultRedirect->setUrl('/');
            return $resultRedirect;
        }

    }
}
