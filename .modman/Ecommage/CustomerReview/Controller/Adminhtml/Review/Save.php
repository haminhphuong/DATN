<?php

namespace Ecommage\CustomerReview\Controller\Adminhtml\Review;

use Ecommage\CustomerReview\Model\ReviewFactory;
use Ecommage\CustomerReview\Model\ResourceModel\Review;
use Magento\Framework\App\Action\HttpPostActionInterface;
use Magento\Backend\App\Action\Context;
/**
 * Class Save
 *
 * @package Ecommage\CustomerReview\Controller\Adminhtml\Customer\Review
 */
class Save extends \Magento\Backend\App\Action implements HttpPostActionInterface
{
    /**
     * @var ReviewFactory
     */
    private $_reviewFactory;
    /**
     * @var Review
     */
    private $reviewResource;

    /**
     * @param Context $context
     * @param ReviewFactory $_reviewFactory
     * @param Review $reviewResource
     */
    public function __construct(
        Context $context,
        ReviewFactory $_reviewFactory,
        Review $reviewResource
    )
    {
        $this->_reviewFactory = $_reviewFactory;
        $this->reviewResource = $reviewResource;
        parent::__construct($context);
    }

    /**
     * @return \Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\ResultInterface
     * @throws \Magento\Framework\Exception\AlreadyExistsException
     * @SuppressWarnings(PHPMD.ElseExpression)
     */
    public function execute()
    {
        $data = $this->getRequest()->getPostValue();
        $data['picture'] = $this->getFileUpload($data, 'picture');
        $data['video'] = $this->getFileUpload($data, 'video');
        $data['image'] = $this->getFileUpload($data, 'image');
//        print_r($data);die;
        $model = $this->_reviewFactory->create();
        $id  = $this->getRequest()->getParam('review_id');
        if($id) {
            $this->reviewResource->load($model, $id);
            if(!$model->getId()){
                $this->messageManager->addErrorsMessage(__('Review dose not exits.'));
            }
        }
        $model->setData($data);
        $this->reviewResource->save($model);
        if ($this->reviewResource->save($model)) {
            $this->messageManager->addSuccessMessage(__('Review was saved successfully.'));
        } else {
            $this->messageManager->addErrorMessage()(__('Failed to save review.'));
        }
        if ($this->getRequest()->getParam('back')) {
            return $this->_redirect('*/*/edit', ['review_id' => $model->getId()]);
        }
        return $this->_redirect('*/*/');
    }

    /**
     * @param array $rawData
     * @param $fieldName
     * @SuppressWarnings(PHPMD.ElseExpression)
     * @return mixed|null
     */
    public function getFileUpload(array $rawData, $fieldName)
    {
        $data = $rawData;
        if (isset($data[$fieldName][0]['name'])) {
            $data[$fieldName] = $data[$fieldName][0]['name'];
        } else {
            $data[$fieldName] = null;
        }
        return $data[$fieldName];
    }

    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Ecommage_CustomerReview::customer_review');
    }
}
