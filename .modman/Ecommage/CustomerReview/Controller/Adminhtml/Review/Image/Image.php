<?php

namespace Ecommage\CustomerReview\Controller\Adminhtml\Review\Image;

use Magento\Backend\App\Action;
use Magento\Framework\App\Action\HttpPostActionInterface;
use Magento\Framework\Controller\ResultFactory;
use Magento\Backend\App\Action\Context;
use Ecommage\CustomerReview\Model\ImageUpload;

/**
 * Class Upload
 *
 * @package Ecommage\CustomerReview\Controller\Adminhtml\Customer\Review\Image
 */
class Image extends Action implements HttpPostActionInterface
{
    /**
     * @var \Magento\Catalog\Model\ImageUploader
     */
    protected $imageUpload;

    /**
     * Upload constructor.
     *
     * @param \Magento\Backend\App\Action\Context  $context
     */
    public function __construct(
        Context $context,
        ImageUpload $imageUpload
    )
    {
        parent::__construct($context);
        $this->imageUpload = $imageUpload;
    }

    /**
     * @return mixed
     */
    public function execute()
    {
        $imageId = $this->_request->getParam('param_name', 'image');

        try {
            $result = $this->imageUpload->saveFileToTmpDir($imageId);
        } catch (\Exception $e) {
            $result = ['error' => $e->getMessage().$e->getTraceAsString(), 'errorcode' => $e->getCode()];
        }
        return $this->resultFactory->create(ResultFactory::TYPE_JSON)->setData($result);
    }
}
