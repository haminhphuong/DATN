<?php
namespace Ecommage\Recommendation\Controller\Checkout;

use Magento\Backend\App\Action\Context;
use Magento\Framework\View\LayoutFactory;
use Magento\Framework\Controller\Result\RawFactory;
use Ecommage\Recommendation\Helper\Data;

class Recommendation extends \Magento\Framework\App\Action\Action
{
    /**
     * @var LayoutFactory
     */
    protected $layoutFactory;
    /**
     * @var RawFactory
     */
    protected $resultRawFactory;
    /**
     * @var Data
     */
    protected $helper;

    /**
     * @param Context $context
     * @param LayoutFactory $layoutFactory
     * @param RawFactory $rawFactory
     */
    public function __construct
    (
        Context $context,
        LayoutFactory $layoutFactory,
        RawFactory $rawFactory,
        Data $helper
    )
    {
        parent::__construct($context);
        $this->layoutFactory = $layoutFactory;
        $this->resultRawFactory = $rawFactory;
        $this->helper = $helper;
    }

    public function execute()
    {
        $type = $this->getRequest()->getParam('type');
        $ids = $this->getRequest()->getParam('productIds');
        $params = $this->getRequest()->getParam('params');
        $block = $this->layoutFactory->create()->createBlock(
            \Ecommage\Recommendation\Block\Checkout\Recommendation::class,
            'checkout.recommendation'.$type,
        [
                'data' => [
                    'type'=>$type,
                    'productIds'=>$ids,
                    'params'=>$params
                ]
            ]
        );
        $html = $this->helper->convertWebp($block->toHtml(),'general');
        /** @var \Magento\Framework\Controller\Result\Raw $resultRaw */
        $resultRaw = $this->resultRawFactory->create();
        return $resultRaw->setContents(
            $html
        );
    }
}
