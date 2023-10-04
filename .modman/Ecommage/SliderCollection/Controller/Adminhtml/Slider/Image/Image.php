<?php

namespace Ecommage\SliderCollection\Controller\Adminhtml\Slider\Image;

use Magento\Backend\App\Action;
use Magento\Framework\App\Action\HttpPostActionInterface;
use Magento\Framework\Controller\ResultFactory;
use Magento\Backend\App\Action\Context;
use Ecommage\SliderCollection\Model\ImageUpload;

/**
 * Class Upload
 *
 * @package Ecommage\SliderCollection\Controller\Adminhtml\Customer\Slider\Image
 */
class Image extends Action implements HttpPostActionInterface
{
    /**
     * @var \Magento\Catalog\Model\ImageUploader
     */
    protected $imageUpload;


    /**
     * @param Context $context
     * @param ImageUpload $imageUpload
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
