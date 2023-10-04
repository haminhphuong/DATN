<?php

namespace Ecommage\BannerManager\Controller\Adminhtml\Item;

class Index extends BannerItem
{
    public function execute()
    {
        $resultPage = $this->_resultPageFactory->create();
        $resultPage->setActiveMenu(self::ADMIN_RESOURCE);
        $resultPage->getConfig()->getTitle()->prepend(__('Banner Item Manager'));
        return $resultPage;
    }
}

