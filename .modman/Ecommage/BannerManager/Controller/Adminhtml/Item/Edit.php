<?php

namespace Ecommage\BannerManager\Controller\Adminhtml\Item;

use Magento\Backend\Model\View\Result\Page;

/**
 * Class Edit
 */
class Edit extends BannerItem
{
    public function execute()
    {
        $id    = $this->getRequest()->getParam('id');
        $model = $this->_itemFactory->create();
        if ($id) {
            $model->load($id);
            if (!$model->getId()) {
                $this->messageManager->addError(__('This Banner Item no longer exists.'));
                $this->_redirect('*/*/');
                return;
            }
        }

        $this->dataPersistor->set('banner_item', $model->getData());
        /** @var Page $resultPage */
        $resultPage = $this->_resultPageFactory->create();
        $resultPage->setActiveMenu(self::ADMIN_RESOURCE);
        $resultPage->getConfig()->getTitle()->prepend(__('Banner Item Edit'));
        return $resultPage;
    }
}
