<?php

namespace Ecommage\BannerManager\Controller\Adminhtml\Index;

class Index extends Banner
{
    public function execute()
    {
        $resultPage = $this->_resultPageFactory->create();
        $resultPage->setActiveMenu(self::ADMIN_RESOURCE);
        $resultPage->getConfig()->getTitle()->prepend(__('Banner Manager'));
        return $resultPage;
    }
}

