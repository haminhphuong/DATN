<?php
/**
 * @author    Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package   Amasty_Storelocator
 */

namespace Ecommage\CustomStoreLocation\Model;

use Exception;
use Magento\Catalog\Model\ImageUploader;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Exception\FileSystemException;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\File\Uploader;
use Magento\Framework\Filesystem;
use Magento\Framework\Filesystem\Directory\WriteInterface;
use Magento\Framework\Image;
use Magento\Framework\ImageFactory;
use Magento\Framework\Message\ManagerInterface;
use Magento\Framework\UrlInterface;
use Magento\MediaStorage\Helper\File\Storage\Database;
use Magento\Store\Model\StoreManagerInterface;
use Psr\Log\LoggerInterface;

class ImageProcessor extends \Amasty\Storelocator\Model\ImageProcessor
{
    /**
     * Locator area inside media folder
     */
    const AMLOCATOR_MEDIA_PATH = 'amasty/amlocator';

    /**
     * Locator temporary area inside media folder
     */
    const AMLOCATOR_MEDIA_TMP_PATH = 'amasty/amlocator/tmp';

    /**
     * Gallery area inside media folder
     */
    const AMLOCATOR_GALLERY_MEDIA_PATH = 'amasty/amlocator/gallery';

    /**
     * Gallery temporary area inside media folder
     */
    const AMLOCATOR_GALLERY_MEDIA_TMP_PATH = 'amasty/amlocator/gallery/tmp';

    /**
     * Type image option marker_img
     */
    const MARKER_IMAGE_TYPE = 'marker_img';

    /**
     * Type image option gallery_image
     */
    const GALLERY_IMAGE_TYPE = 'gallery_image';

    /**
     * @var ImageUploader
     */
    private $imageUploader;

    /**
     * @var ImageFactory
     */
    private $imageFactory;

    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var WriteInterface
     */
    private $mediaDirectory;

    /**
     * @var Filesystem
     */
    private $filesystem;

    /**
     * @var ManagerInterface
     */
    private $messageManager;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * Allowed extensions
     *
     * @var array
     */
    protected $allowedExtensions = ['jpg', 'jpeg', 'gif', 'png'];

    /**
     * @var Database
     */
    protected $coreFileStorageDatabase;

    public function __construct(
        Filesystem $filesystem,
        ImageUploader $imageUploader,
        ImageFactory $imageFactory,
        StoreManagerInterface $storeManager,
        ManagerInterface $messageManager,
        LoggerInterface $logger,
        Database $coreFileStorageDatabase

    ) {
        $this->filesystem              = $filesystem;
        $this->imageUploader           = $imageUploader;
        $this->imageFactory            = $imageFactory;
        $this->storeManager            = $storeManager;
        $this->messageManager          = $messageManager;
        $this->logger                  = $logger;
        $this->coreFileStorageDatabase = $coreFileStorageDatabase;
        $this->mediaDirectory          = $filesystem->getDirectoryWrite(DirectoryList::MEDIA);

    }

    /**
     * @return WriteInterface
     * @throws FileSystemException
     */
    private function getMediaDirectory()
    {
        if ($this->mediaDirectory === null) {
            $this->mediaDirectory = $this->filesystem->getDirectoryWrite(DirectoryList::MEDIA);
        }

        return $this->mediaDirectory;
    }

    /**
     * @param string $imageName
     *
     * @return string
     */
    public function getImageRelativePath($imageName)
    {
        return $this->imageUploader->getBasePath() . DIRECTORY_SEPARATOR . $imageName;
    }

    /**
     *
     * @return string
     */
    public function getMediaUrl()
    {
        return $this->storeManager
            ->getStore()
            ->getBaseUrl(UrlInterface::URL_TYPE_MEDIA);
    }

    /**
     * @param array $params
     *
     * @return string
     */
    public function getImageUrl($params = [])
    {
        return $this->getMediaUrl() . implode(DIRECTORY_SEPARATOR, $params);
    }

    /**
     * Move file from temporary directory
     *
     * @param string $imageName
     * @param string $imageType
     * @param int    $locationId
     * @param bool   $locationIsNew
     *
     * @throws LocalizedException
     */
    public function processImage($imageName, $imageType, $locationId, $locationIsNew)
    {
        $this->setBasePaths($imageType, $locationId, $locationIsNew);
        $this->moveFileFromTmpCustom($imageName, $locationId, true, $locationIsNew);

        $filename = $this->getMediaDirectory()->getAbsolutePath($this->getImageRelativePath($imageName));
        try {
            $this->prepareImage($filename, $imageType);
        } catch (Exception $e) {
            $errorMessage = $e->getMessage();
            $this->messageManager->addErrorMessage(
                __($errorMessage)
            );
            $this->logger->critical($e);
        }
    }

    /**
     * @param string $filename
     * @param string $imageType
     * @param bool   $needResize
     * @SuppressWarnings(PHPMD.BooleanArgumentFlag)
     */
    public function prepareImage($filename, $imageType, $needResize = false)
    {
        $imageProcessor = $this->imageFactory->create(['fileName' => $filename]);
        $imageProcessor->keepAspectRatio(true);
        $imageProcessor->keepFrame(true);
        $imageProcessor->keepTransparency(true);
        if ($imageType == self::MARKER_IMAGE_TYPE || $needResize) {
            $imageProcessor->resize(27, 43);
        }
        $imageProcessor->save();
    }

    /**
     * @param string $imageName
     *
     * @throws FileSystemException
     */
    public function deleteImage($imageName)
    {
        $this->getMediaDirectory()->delete(
            $this->getImageRelativePath($imageName)
        );
    }

    /**
     * @param string $imageType
     * @param int    $locationId
     * @param bool   $locationIsNew
     */
    public function setBasePaths($imageType, $locationId, $locationIsNew)
    {
        // if location doesn't exist, we set 0 to tmp path
        $tmpLocationId = $locationIsNew ? 0 : $locationId;
        $tmpPath       = ImageProcessor::AMLOCATOR_MEDIA_TMP_PATH . DIRECTORY_SEPARATOR . $tmpLocationId;
        $this->imageUploader->setBaseTmpPath(
            $tmpPath
        );
        switch ($imageType) {
            case ImageProcessor::MARKER_IMAGE_TYPE:
                $this->imageUploader->setBasePath(
                    ImageProcessor::AMLOCATOR_MEDIA_PATH . DIRECTORY_SEPARATOR . $locationId
                );
                break;

            case ImageProcessor::GALLERY_IMAGE_TYPE:
                $this->imageUploader->setBasePath(
                    ImageProcessor::AMLOCATOR_GALLERY_MEDIA_PATH . DIRECTORY_SEPARATOR . $locationId
                );
                break;
        }
    }

    /**
     * @param      $imageName
     * @param      $locationId
     * @param bool $returnRelativePath
     *
     * @param      $locationIsNew
     *
     * @return string
     * @throws LocalizedException
     * @SuppressWarnings(PHPMD.BooleanArgumentFlag)
     * @SuppressWarnings(PHPMD.StaticAccess)
     */
    public function moveFileFromTmpCustom($imageName, $locationId, $returnRelativePath = false, $locationIsNew)
    {
        $baseTmpPath = self::AMLOCATOR_MEDIA_TMP_PATH . DIRECTORY_SEPARATOR . 0;
        if (!$locationIsNew) {
            $baseTmpPath = self::AMLOCATOR_MEDIA_TMP_PATH . DIRECTORY_SEPARATOR . $locationId;
        }
        $basePath      = $this->imageUploader->getBasePath();
        $baseImagePath = $this->getFilePath(
            $basePath,
            Uploader::getNewFileName(
                $this->mediaDirectory->getAbsolutePath(
                    $this->getFilePath($basePath, $imageName)
                )
            )
        );

        $baseTmpImagePath = $this->getFilePath($baseTmpPath, $imageName);

        try {
            $this->coreFileStorageDatabase->copyFile(
                $baseTmpImagePath,
                $baseImagePath
            );

            $this->mediaDirectory->renameFile(
                $baseTmpImagePath,
                $baseImagePath
            );

        } catch (Exception $e) {
            $this->logger->critical($e);
            throw new LocalizedException(
                __('Something went wrong while saving the file(s).'),
                $e
            );
        }

        return $returnRelativePath ? $baseImagePath : $imageName;
    }

    /**
     * @param $path
     * @param $imageName
     *
     * @return string
     */
    public function getFilePath($path, $imageName)
    {
        return rtrim($path, '/') . '/' . ltrim($imageName, '/');
    }
}
