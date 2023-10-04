<?php

namespace Ecommage\CustomerReview\Controller\Adminhtml\Review;

use Exception;
use Ecommage\CustomerReview\Model\FileUploader;
use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\App\Action\HttpPostActionInterface;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Controller\ResultInterface;
use tests\unit\Magento\FunctionalTestFramework\Console\BaseGenerateCommandTest;

class Upload extends Action implements HttpPostActionInterface
{
    /**
     * Image uploader
     *
     * @var ImageUploader
     */
    protected $fileUploader;

    /**
     * Upload constructor.
     *
     * @param Context $context
     * @param ImageUploader $imageUploader
     */
    public function __construct
    (
        Context $context,
        FileUploader $fileUploader
    )
    {
        parent::__construct($context);
        $this->fileUploader = $fileUploader;
    }
    /**
     * Upload file controller action
     *
     * @return ResultInterface
     */
    public function execute()
    {
        $videoid = $this->_request->getParam('param_name', 'video');
        try {
            $result = $this->fileUploader->saveFileToTmpDir($videoid);
        } catch (Exception $e) {
            $result = ['error' => $e->getMessage(), 'errorcode' => $e->getCode()];
        }
        return $this->resultFactory->create(ResultFactory::TYPE_JSON)->setData($result);
    }
}
