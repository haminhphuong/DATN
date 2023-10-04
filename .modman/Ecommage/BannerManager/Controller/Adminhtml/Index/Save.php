<?php

namespace Ecommage\BannerManager\Controller\Adminhtml\Index;

use Ecommage\BannerManager\Model\BannerFactory;
use Ecommage\BannerManager\Model\BannerRepository;
use Exception;
use Magento\Backend\App\Action\Context;
use Magento\Backend\Model\View\Result\Redirect;
use Magento\Framework\App\Request\DataPersistorInterface;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Exception\LocalizedException;
use Ecommage\BannerManager\Api\Data\BannerInterface as BN;
use Magento\Framework\View\Result\PageFactory;
use Ecommage\BannerManager\Model\System\Config\Status;
use Magento\Framework\App\Config\ScopeConfigInterface;

/**
 * Class Save
 */
class Save extends Banner
{
    /**
     * @var ScopeConfigInterface
     */
    protected $scopeConfig;
    /**
     * @var DataPersistorInterface
     */
    protected $dataPersistor;
    /**
     * @var BannerRepository
     */
    protected $bannerRepository;

    /**
     * Save constructor.
     *
     * @param Context                $context
     * @param BannerFactory          $bannerFactory
     * @param BannerRepository       $bannerRepository
     * @param DataPersistorInterface $dataPersistor
     * @param PageFactory            $resultPageFactory
     */
    public function __construct(
        Context                $context,
        BannerFactory          $bannerFactory,
        BannerRepository       $bannerRepository,
        DataPersistorInterface $dataPersistor,
        ScopeConfigInterface   $scopeConfig,
        PageFactory            $resultPageFactory
    ) {
        $this->scopeConfig      = $scopeConfig;
        $this->bannerRepository = $bannerRepository;
        parent::__construct($context, $bannerFactory, $dataPersistor, $resultPageFactory);
    }

    /**
     * Save action
     *
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @return ResultInterface
     */
    public function execute()
    {
        $banner = $this->getRequest()->getParam(BN::FORM_GENERAL);
        /** @var Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();
        if ($banner) {
            $banner['options'] = $this->prepareOptions();
            if (isset($banner[BN::IS_ACTIVE]) && $banner[BN::IS_ACTIVE] === 'true') {
                $banner[BN::IS_ACTIVE] = Status::STATUS_ENABLED;
            }

            if (empty($banner[BN::BANNER_ID])) {
                $banner[BN::BANNER_ID] = null;
            }

            /** @var \Ecommage\BannerManager\Model\Banner $model */
            $model = $this->_bannerFactory->create();
            $id    = $this->getId();
            if ($id) {
                $model = $this->bannerRepository->getById($id);
            }

            $model->setData($banner);
            try {
                $this->bannerRepository->save($model);
                $this->messageManager->addSuccess(__('You saved the thing.'));
                $this->dataPersistor->clear('banner_entity');
                if ($this->getRequest()->getParam('back')) {
                    $path = '*/*/edit';
                    if ($id && !empty($model->getBannerItems())) {
                        $path = '*/*/preview';
                    }

                    if (empty($model->getBannerItems())) {
                        $this->messageManager->addNotice(__('To preview banner you have to add banner item.'));
                    }

                    return $resultRedirect->setPath($path, [BN::BANNER_ID => $model->getId(), '_current' => true]);
                }
                return $resultRedirect->setPath('*/*/');
            } catch (LocalizedException $e) {
                $this->messageManager->addError($e->getMessage());
            } catch (Exception $e) {
                $this->messageManager->addException($e, __('Something went wrong while saving the data.'));
            }

            $this->dataPersistor->set('banner_entity', $banner);
            return $resultRedirect->setPath('*/*/edit', [BN::BANNER_ID => $id]);
        }
        return $resultRedirect->setPath('*/*/');
    }

    /**
     * @return mixed|null
     */
    public function getId()
    {
        $bannerId = $this->getRequest()->getParam(BN::BANNER_ID);
        if (empty($bannerId)) {
            $banner = $this->getRequest()->getParam('banner');
            $bannerId = $banner[BN::BANNER_ID] ?? null;
        }

        return $bannerId;
    }

    /**
     * @return array
     */
    protected function prepareOptions()
    {
        $options        = [];
        $bannerOptions  = $this->getRequest()->getParam(BN::FORM_OPTIONS);
        $defaultOptions = $this->scopeConfig->getValue('ecommage/banner/options');
        if (is_array($bannerOptions) && !empty($bannerOptions)) {
            foreach ($bannerOptions as $optionKey => $optionValue) {
                if (isset($defaultOptions[$optionKey]) && $defaultOptions[$optionKey] !== $optionValue) {
                    $options[$optionKey] = $optionValue;
                }
            }
        }
        return $options;
    }
}
