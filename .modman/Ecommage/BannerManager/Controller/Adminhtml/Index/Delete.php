<?php

namespace Ecommage\BannerManager\Controller\Adminhtml\Index;

use Exception;
use Ecommage\BannerManager\Model\Banner as BN;
/**
 * Class Delete
 */
class Delete extends Banner
{
    /**
     * @SuppressWarnings(PHPMD.ElseExpression)
     * @return \Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        $id = $this->getRequest()->getParam(BN::BANNER_ID);
        if ($id) {
            $model = $this->_bannerFactory->create();
            $model->load($id);
            if (!$model->getId()) {
                $this->messageManager->addError(__('Banner is no longer exist'));
            } else {
                try {
                    $model->delete();
                    $this->messageManager->addSuccess(__('Deleted Successfully!'));
                } catch (Exception $e) {
                    $this->messageManager->addError($e->getMessage());
                    $this->_redirect('*/*/edit', [BN::BANNER_ID => $model->getId()]);
                }
            }
        }
        return $this->_redirect('*/*/');
    }
}
