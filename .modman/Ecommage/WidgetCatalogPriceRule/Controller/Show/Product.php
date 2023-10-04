<?php

namespace Ecommage\WidgetCatalogPriceRule\Controller\Show;

use Magento\Framework\App\Action\Context;
use Magento\Framework\App\ResponseInterface;

class Product extends \Magento\Framework\App\Action\Action

{
    /**
     * @var \Magento\Framework\View\Result\PageFactory
     */
    protected $resultPageFactory;
    /**
     * @var \Magento\Framework\Controller\Result\JsonFactory
     */
    protected $resultJsonFactory;
    /**
     * @param Context $context
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     * @param \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory
     */
    public function __construct(
        Context $context,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory
    ) {
        $this->resultPageFactory = $resultPageFactory;
        $this->resultJsonFactory = $resultJsonFactory;
        parent::__construct($context);
    }
    public function execute()
    {
        $pageSize = $this->getRequest()->getParam('page_size',false);
        $curPage = $this->getRequest()->getParam('current_page');
        $categoryId = $this->getRequest()->getParam('show_all');
        $html = $this->_view->getLayout()
            ->createBlock('Ecommage\WidgetCatalogPriceRule\Block\Widget\CatalogPriceRule',
                'ecommage_limit_product',
                [
                    'data' => [
                        'page_size' => $pageSize,
                        'current_page' => $curPage,
                        'show_all' => $categoryId
                    ]
                ])
            ->setTemplate('Ecommage_WidgetCatalogPriceRule::widget/moredata/getmore.phtml')->toHtml();
        $resultPage = $this->resultJsonFactory->create();
        return $resultPage->setData(['html' => $html]);
    }
}
