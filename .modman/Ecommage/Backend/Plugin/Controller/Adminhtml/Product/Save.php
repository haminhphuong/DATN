<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Ecommage\Backend\Plugin\Controller\Adminhtml\Product;

use Magento\Framework\Controller\Result\RedirectFactory;
use Ecommage\Backend\Model\ResourceModel\ScheduleProduct\Collection as ScheduleCollection;
use Ecommage\Backend\Model\ScheduleProduct;
use Magento\Catalog\Model\ProductRepository;
use Magento\Framework\Message\ManagerInterface;
use Psr\Log\LoggerInterface;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Filesystem;
use Magento\Framework\App\Response\Http\FileFactory;
use Magento\Catalog\Api\ProductAttributeManagementInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;

/**
 * Product save controller
 *
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Save
{
    const PATH_SCHEDULE_ALLOW_ATTRIBUTE = 'schedule_allow_attribute/configuration/attribute';
    /**
     * @var RedirectFactory
     */
    protected $redirectFactory;
    /**
     * @var ScheduleCollection
     */
    protected $sheduleCollection;
    /**
     * @var ScheduleProduct
     */
    protected $scheduleProduct;
    /**
     * @var ProductRepository
     */
    protected $productRepository;
    /**
     * @var ManagerInterface
     */
    protected $messageManager;
    /**
     * @var LoggerInterface
     */
    protected $logger;
    protected $directory;
    /**
     * @var Filesystem
     */
    protected $filesystem;
    /**
     * @var FileFactory
     */
    protected $fileFactory;
    /**
     * @var ProductAttributeManagementInterface
     */
    protected $productAttributeManagement;
    /**
     * @var ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * @param RedirectFactory $redirectFactory
     * @param ScheduleCollection $sheduleCollection
     * @param ScheduleProduct $scheduleProduct
     * @param ProductRepository $productRepository
     * @param ManagerInterface $messageManager
     * @param LoggerInterface $logger
     * @param Filesystem $filesystem
     * @param FileFactory $fileFactory
     * @param ProductAttributeManagementInterface $productAttributeManagement
     * @param ScopeConfigInterface $scopeConfig
     */
    public function __construct
    (
        RedirectFactory $redirectFactory,
        ScheduleCollection $sheduleCollection,
        ScheduleProduct $scheduleProduct,
        ProductRepository $productRepository,
        ManagerInterface $messageManager,
        LoggerInterface $logger,
        Filesystem $filesystem,
        FileFactory $fileFactory,
        ProductAttributeManagementInterface $productAttributeManagement,
        ScopeConfigInterface $scopeConfig
    )
    {
        $this->redirectFactory = $redirectFactory;
        $this->sheduleCollection = $sheduleCollection;
        $this->scheduleProduct = $scheduleProduct;
        $this->productRepository = $productRepository;
        $this->messageManager = $messageManager;
        $this->logger = $logger;
        $this->filesystem = $filesystem;
        $this->fileFactory = $fileFactory;
        $this->productAttributeManagement = $productAttributeManagement;
        $this->scopeConfig = $scopeConfig;
    }

    /**
     * @param \Magento\Catalog\Controller\Adminhtml\Product\Save $subject
     * @param \Closure $proceed
     * @return \Magento\Framework\Controller\Result\Redirect|mixed
     * @throws \Magento\Framework\Exception\FileSystemException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function aroundExecute(\Magento\Catalog\Controller\Adminhtml\Product\Save $subject, \Closure $proceed)
    {
        $redirectBack = $subject->getRequest()->getParam('back', false);
        if($redirectBack == 'schedule'){
            $resultRedirect = $this->redirectFactory->create();
            $data = $subject->getRequest()->getPostValue();
            $dataProduct = $data['product'];
            $productId = isset($dataProduct['current_product_id']) ? $dataProduct['current_product_id'] : '';
            $storeId = isset($dataProduct['current_store_id']) ? $dataProduct['current_store_id'] : 0;
            $scheduleChangeStart = isset($dataProduct['schedule_change_start']) ? $dataProduct['schedule_change_start'] : '';
            $scheduleChangeEnd = isset($dataProduct['schedule_change_end']) ? $dataProduct['schedule_change_end'] : '';
            $info = [];
            $attr = $this->scopeConfig->getValue('schedule_allow_attribute/configuration/attribute');
            $attributes = explode(',', $attr);

            foreach ($dataProduct as $key=>$value){
                if(in_array($key,$attributes) && (!is_array($value))){
                    $info[$key] = $value ?: '';
                }
            }

            $dataJson = json_encode($info);

            if(!$productId){
                $this->messageManager->addErrorMessage(__('Change scheduling only works for existing products!'));
                $resultRedirect->setPath(
                    'catalog/*/new',
                    ['back' => null, '_current' => true]
                );
                return $resultRedirect;
            }
            elseif(!$scheduleChangeStart || !$scheduleChangeEnd){
                $this->messageManager->addErrorMessage(__('Schedule Change is required!'));
            }
            else{
                $product = $this->productRepository->getById($productId);

                $scheduled = $this->sheduleCollection->addFieldToFilter('product_id',$productId)->addFieldToFilter('store_id',$storeId)->getFirstItem();
                if($scheduledId = $scheduled->getId()){
                    $this->scheduleProduct->load($scheduledId);
                }
                $this->scheduleProduct->setProductId($productId);
                $this->scheduleProduct->setStoreId($storeId);
                $this->scheduleProduct->setInfo($dataJson);
                $this->scheduleProduct->setScheduleDateStart($scheduleChangeStart);
                $this->scheduleProduct->setScheduleDateEnd($scheduleChangeEnd);
                $this->scheduleProduct->setStatus(1);
                try {
                    $this->scheduleProduct->save();
                }catch (\Exception $e){
                    $this->logger->debug($e->getMessage());
                }
                $this->renderfile($product,$info,$this->scheduleProduct->getId());
                $product->addAttributeUpdate('schedule_change_start',$scheduleChangeStart,$storeId);
                $product->addAttributeUpdate('schedule_change_end',$scheduleChangeEnd,$storeId);
                $this->messageManager->addSuccessMessage(__('Product '.$product->getName().' has been successfully scheduled!'));
            }

            $resultRedirect->setPath(
                'catalog/*/edit',
                ['id' => $productId, 'back' => null, '_current' => true]
            );
            return $resultRedirect;
        }else{
            return $proceed();
        }
    }

    /**
     * @param $info
     * @return array
     */
    public function getColumnHeader($info) {
        $headers = [];
        foreach ($info as $key => $value) {
            if(!is_array($value)){
                $headers[] = $key;
            }
        }
        return $headers;
    }

    /**
     * @param $product
     * @param $info
     * @param $scheduleId
     * @return void
     * @throws \Magento\Framework\Exception\FileSystemException
     */
    public function renderfile($product, $info, $scheduleId){
        /**
         * @var \Magento\Catalog\Model\Product $product
         */
        $this->directory = $this->filesystem->getDirectoryWrite(DirectoryList::VAR_DIR);
        $filepath = 'schedule/schedule_product_'.$scheduleId.'_'.$product->getId() . '.csv';
        $this->directory->delete($filepath);
        $this->directory->create('export');
        /* Open file */
        $stream = $this->directory->openFile($filepath, 'w+');
        $stream->lock();
        $columns = $this->getColumnHeader($info);
        foreach ($columns as $column) {
            $header[] = $column;
        }
        /* Write Header */
        $stream->writeCsv($header);
        $itemData = [];
        foreach ($info as $key=>$item) {
            $text = $item;
            if($item){
                if(is_array($item)){
                    $text = implode("/",$item);
                }else{
                    $attr = $product->getResource()->getAttribute($key);
                    if ($attr->usesSource()) {
                        $text = $attr->getSource()->getOptionText($item);
                    }
                }
            }
            if(is_array($text)){
                $text = isset($text['value']) ? $text['value'] : '';
            }
            $itemData[] = $text ?: ' ';
        }
        $stream->writeCsv($itemData);
    }
}
