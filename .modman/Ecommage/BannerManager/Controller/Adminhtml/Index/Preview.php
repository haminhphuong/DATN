<?php

namespace Ecommage\BannerManager\Controller\Adminhtml\Index;

use Ecommage\BannerManager\Model\Banner;

class Preview extends Edit
{
    public function execute()
    {
        $id     = $this->getRequest()->getParam(Banner::BANNER_ID);
        $banner = $this->_bannerFactory->create()->load($id);
        if (!$banner->getId()) {
            $this->messageManager->addError(__('This Banner no longer exists.'));
            $this->_redirect('*/*/');
            return;
        }

        $resultPage = $this->_resultPageFactory->create();
        $resultPage->getConfig()->getTitle()->prepend(__('Preivew Banner'));
        return $resultPage;
    }
}

