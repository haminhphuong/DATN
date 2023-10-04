<?php

namespace Ecommage\CustomOptionsImage\Controller\Adminhtml\Option;

use Magento\Framework\Controller\ResultFactory;

class Upload extends \Magento\Backend\App\Action
{
    /**
     * @var \Magento\MediaStorage\Model\File\UploaderFactory
     */
    protected $imageUploader;

    /**
     * @var \Magento\Framework\Filesystem\Directory\WriteInterface
     */
    protected $_fileSystem;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;


    /**
     * Upload constructor.
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\MediaStorage\Model\File\UploaderFactory $fileUploaderFactory
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Framework\Filesystem $filesystem
     * @throws \Magento\Framework\Exception\FileSystemException
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\MediaStorage\Model\File\UploaderFactory $fileUploaderFactory,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\Filesystem $filesystem
    ) {
        parent::__construct($context);
        $this->imageUploader = $fileUploaderFactory;
        $this->_storeManager = $storeManager;
        $this->_fileSystem = $filesystem->getDirectoryWrite(\Magento\Framework\App\Filesystem\DirectoryList::MEDIA);
    }

    /**
     * @return \Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        try {
            $uploader = $this->imageUploader->create(['fileId' => 'image']);
            $uploader->setAllowedExtensions(array('jpg', 'jpeg', 'gif', 'png'));
            $uploader->setAllowCreateFolders(true);
            $uploader->setAllowRenameFiles(true);
            $uploader->setFilesDispersion(true);
            $path = $this->_fileSystem->getAbsolutePath('Ecommage/CustomOption');
            $result = $uploader->save($path, $_FILES['image']['name']);
            $result['db_file'] = $this->_fileSystem->getRelativePath('Ecommage/CustomOption') . $uploader->getUploadedFileName();
            $result['url'] = $this->_storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA)
                . $this->_fileSystem->getRelativePath('Ecommage_slider/image') .
                $uploader->getUploadedFileName();
        } catch (\Exception $e) {
            $result = ['error' => $e->getMessage(), 'errorcode' => $e->getCode()];
        }
        return $this->resultFactory->create(ResultFactory::TYPE_JSON)->setData($result);
    }
}
