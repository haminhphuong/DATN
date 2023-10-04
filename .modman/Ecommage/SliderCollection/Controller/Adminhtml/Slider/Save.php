<?php

namespace Ecommage\SliderCollection\Controller\Adminhtml\Slider;

use Ecommage\SliderCollection\Model\ImageUpload;
use Ecommage\SliderCollection\Model\SliderFactory;
use Ecommage\SliderCollection\Model\ResourceModel\Slider;
use Magento\Framework\App\Action\HttpPostActionInterface;
use Magento\Backend\App\Action\Context;
/**
 * Class Save
 *
 * @package Ecommage\SliderCollection\Controller\Adminhtml\Customer\Slider
 */
class Save extends \Magento\Backend\App\Action implements HttpPostActionInterface
{
    /**
     * @var SliderFactory
     */
    private $_sliderFactory;
    /**
     * @var Slider
     */
    private $sliderResource;
    /**
     * @var ImageUpload
     */
    protected $imageUploader;

    /**
     * @param Context $context
     * @param SliderFactory $_sliderFactory
     * @param Slider $sliderResource
     * @param ImageUpload $imageUploader
     */
    public function __construct(
        Context $context,
        SliderFactory $_sliderFactory,
        Slider $sliderResource,
        ImageUpload $imageUploader
    )
    {
        $this->_sliderFactory = $_sliderFactory;
        $this->sliderResource = $sliderResource;
        $this->imageUploader = $imageUploader;
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

       $data['image'] = $this->getFileUpload($data, 'image');
       $data['image_watch'] = $this->getFileUpload($data, 'image_watch');
        $data['store_id'] = $data['store_id'][0];
        $model = $this->_sliderFactory->create();
        $id  = $this->getRequest()->getParam('slider_id');
        if($id) {
            $this->sliderResource->load($model, $id);
            if(!$model->getId()){
                $this->messageManager->addErrorsMessage(__('Slider dose not exits.'));
            }
        }

        $model->setData($data);
        $this->sliderResource->save($model);
        if ($this->sliderResource->save($model)) {
            $this->messageManager->addSuccessMessage(__('Slider was saved successfully.'));
        } else {
            $this->messageManager->addErrorMessage()(__('Failed to save slider.'));
        }
        if ($this->getRequest()->getParam('back')) {
            return $this->_redirect('*/*/edit', ['slider_id' => $model->getId()]); // @codingStandardsIgnoreFile
        }
        return $this->_redirect('*/*/'); // @codingStandardsIgnoreFile
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
        $image = $data[$fieldName];
        if (isset($image[0]['name'])) {
            $urlImage = $image[0]['url'];
            if (!empty($image[0]['tmp_name'])) {
                $this->imageUploader->moveFileFromTmp($image);
                $urlImage = str_replace('slider_collection/tmp/images','slider_collection/images', $urlImage);
            }
            $image = substr($urlImage, strpos($urlImage, '/media/') + 7);
        }
        return $image;
    }

    /**
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Ecommage_SliderCollection::slider_collection');
    }
}
