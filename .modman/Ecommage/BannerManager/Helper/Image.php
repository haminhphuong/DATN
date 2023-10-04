<?php

namespace Ecommage\BannerManager\Helper;

use Magento\Framework\UrlInterface;
use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Filesystem\DirectoryList;
use Ecommage\BannerManager\Model\Banner\FileInfo;
// @codingStandardsIgnoreFile
class Image extends AbstractHelper
{
    /**
     * @var \Magento\Framework\Filesystem
     */
    protected $_filesystem;
    /**
     * @var \Magento\Framework\Image\AdapterFactory
     */
    protected $_imageFactory;
    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;
    /**
     * @var \Magento\Framework\View\Asset\Repository
     */
    protected $_assetRepo;

    /**
     * Image constructor.
     *
     * @param \Magento\Framework\Filesystem              $filesystem
     * @param \Magento\Framework\Image\AdapterFactory    $imageFactory
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Framework\App\Helper\Context      $context
     */
    public function __construct(
        \Magento\Framework\Filesystem $filesystem,
        \Magento\Framework\Image\AdapterFactory $imageFactory,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\View\Asset\Repository $assetRepo,
        \Magento\Framework\App\Helper\Context $context
    ) {
        $this->_filesystem   = $filesystem;
        $this->_imageFactory = $imageFactory;
        $this->_storeManager = $storeManager;
        $this->_assetRepo    = $assetRepo;
        parent::__construct($context);
    }

    /**
     * @param      $image
     * @param null $width
     * @param null $height
     *
     * @return bool|string
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function resize($image, $width = 60, $height = 60)
    {
        $directoryRead = $this->_filesystem->getDirectoryRead(DirectoryList::MEDIA);
        $absolutePath  = $directoryRead->getAbsolutePath(FileInfo::ENTITY_MEDIA_PATH) . DIRECTORY_SEPARATOR . $image;
        if (!file_exists($absolutePath)) {
            return false;
        }

        $resizedPath  = "banner/{$width}/{$height}/";
        $imageResized = $directoryRead->getAbsolutePath($resizedPath) . $image;
        if (!file_exists($imageResized)) { // Only resize image if not already exists.
            //create image factory...
            $imageResize = $this->_imageFactory->create();
            $imageResize->open($absolutePath);
            $imageResize->constrainOnly(TRUE);
            $imageResize->keepTransparency(TRUE);
            $imageResize->keepFrame(FALSE);
            $imageResize->keepAspectRatio(TRUE);
            $imageResize->resize($width, $height);
            //destination folder
            $destination = $imageResized;
            //save image
            $imageResize->save($destination);
        }
        $path = $resizedPath . $image;
        return $this->getImageUrl($path);
    }

    /**
     * @param $fileName
     *
     * @return string
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getImageUrl($fileName)
    {
        if (empty($fileName)) {
            return $this->_assetRepo->getUrl('Ecommage_BannerManager::images/thumbnail.jpg');
        }

        return $this->_storeManager->getStore()->getBaseUrl(UrlInterface::URL_TYPE_MEDIA) . $fileName;
    }
}
