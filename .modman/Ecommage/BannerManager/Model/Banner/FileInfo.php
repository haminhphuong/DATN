<?php
namespace Ecommage\BannerManager\Model\Banner;

use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\File\Mime;
use Magento\Framework\Filesystem;
use Magento\Framework\Filesystem\Directory\WriteInterface;
use Magento\Framework\Filesystem\Directory\ReadInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Store\Model\StoreManagerInterface;

/**
 * Class FileInfo
 *
 * Provides information about requested file
 */
// @codingStandardsIgnoreFile
class FileInfo
{
    /**
     * Path in /pub/media directory
     */
    const ENTITY_MEDIA_PATH = '/banner/item';

    /**
     * @var Filesystem
     */
    private $filesystem;

    /**
     * @var Mime
     */
    private $mime;

    /**
     * @var WriteInterface
     */
    private $mediaDirectory;

    /**
     * @var ReadInterface
     */
    private $baseDirectory;

    /**
     * @var ReadInterface
     */
    private $pubDirectory;

    /**
     * Store manager
     *
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    private $storeManager;

    /**
     * @param Filesystem $filesystem
     * @param Mime $mime
     * @param StoreManagerInterface $storeManager
     */
    public function __construct(
        Filesystem $filesystem,
        Mime $mime,
        StoreManagerInterface $storeManager
    ) {
        $this->filesystem = $filesystem;
        $this->mime = $mime;
        $this->storeManager = $storeManager;
    }

    /**
     * Get WriteInterface instance
     *
     * @return WriteInterface
     */
    private function getMediaDirectory()
    {
        if ($this->mediaDirectory === null) {
            $this->mediaDirectory = $this->filesystem->getDirectoryWrite(DirectoryList::MEDIA);
        }
        return $this->mediaDirectory;
    }

    /**
     * Get Base Directory read instance
     *
     * @return ReadInterface
     */
    private function getBaseDirectory()
    {
        if (!isset($this->baseDirectory)) {
            $this->baseDirectory = $this->filesystem->getDirectoryRead(DirectoryList::ROOT);
        }

        return $this->baseDirectory;
    }

    /**
     * Get Pub Directory read instance
     *
     * @return ReadInterface
     */
    private function getPubDirectory()
    {
        if (!isset($this->pubDirectory)) {
            $this->pubDirectory = $this->filesystem->getDirectoryRead(DirectoryList::PUB);
        }

        return $this->pubDirectory;
    }

    /**
     * Retrieve MIME type of requested file
     *
     * @param string $fileName
     * @return string
     */
    public function getMimeType($fileName)
    {
        $filePath = $this->getFilePath($fileName);
        $absoluteFilePath = $this->getMediaDirectory()->getAbsolutePath($filePath);

        $result = $this->mime->getMimeType($absoluteFilePath);
        return $result;
    }

    /**
     * Get file statistics data
     *
     * @param string $fileName
     * @return array
     */
    public function getStat($fileName)
    {
        $filePath = $this->getFilePath($fileName);

        $result = $this->getMediaDirectory()->stat($filePath);
        return $result;
    }

    /**
     * Check if the file exists
     *
     * @param string $fileName
     * @return bool
     */
    public function isExist($fileName)
    {
        $filePath = $this->getFilePath($fileName);
        $result = $this->getMediaDirectory()->isExist($filePath);
        return $result;
    }

    /**
     * Construct and return file subpath based on filename relative to media directory
     *
     * @param string $fileName
     * @return string
     * @SuppressWarnings(PHPMD.ElseExpression)
     */
    private function getFilePath($fileName)
    {
        return $fileName;
//        $filePath = $this->removeStorePath($fileName);
//        $filePath = ltrim($filePath, '/');
//
//        $mediaDirectoryRelativeSubpath = $this->getMediaDirectoryPathRelativeToBaseDirectoryPath($filePath);
//        $isFileNameBeginsWithMediaDirectoryPath = $this->isBeginsWithMediaDirectoryPath($fileName);
//
//        // if the file is not using a relative path, it resides in the catalog/category media directory
//        $fileIsInCategoryMediaDir = !$isFileNameBeginsWithMediaDirectoryPath;
//
//        if ($fileIsInCategoryMediaDir) {
//            $filePath = self::ENTITY_MEDIA_PATH . '/' . $filePath;
//        } else {
//            $filePath = substr($filePath, strlen($mediaDirectoryRelativeSubpath));
//        }
//
//        return $filePath;
    }

    /**
     * Checks for whether $fileName string begins with media directory path
     *
     * @param string $fileName
     * @return bool
     */
    public function isBeginsWithMediaDirectoryPath($fileName)
    {
        $filePath = $this->removeStorePath($fileName);
        $filePath = ltrim($filePath, '/');

        $mediaDirectoryRelativeSubpath = $this->getMediaDirectoryPathRelativeToBaseDirectoryPath($filePath);
        $isFileNameBeginsWithMediaDirectoryPath = strpos($filePath, (string) $mediaDirectoryRelativeSubpath) === 0;

        return $isFileNameBeginsWithMediaDirectoryPath;
    }

    /**
     * Clean store path in case if it's exists
     *
     * @param string $path
     * @return string
     */
    private function removeStorePath($path = null): string
    {
        $result = $path;
        try {
            $storeUrl = $this->storeManager->getStore()->getBaseUrl();
        } catch (NoSuchEntityException $e) {
            return $result;
        }
        // phpcs:ignore Magento2.Functions.DiscouragedFunction
        $path = parse_url($path, PHP_URL_PATH);
        // phpcs:ignore Magento2.Functions.DiscouragedFunction
        $storePath = parse_url($storeUrl, PHP_URL_PATH);
        $storePath = rtrim($storePath, '/');

        $result = preg_replace('/^' . preg_quote($storePath, '/') . '/', '', $path);
        return $result;
    }

    /**
     * Get media directory subpath relative to base directory path
     *
     * @param string $filePath
     * @return string
     */
    private function getMediaDirectoryPathRelativeToBaseDirectoryPath(string $filePath = '')
    {
        $baseDirectory = $this->getBaseDirectory();
        $baseDirectoryPath = $baseDirectory->getAbsolutePath();
        $mediaDirectoryPath = $this->getMediaDirectory()->getAbsolutePath();
        $pubDirectoryPath = $this->getPubDirectory()->getAbsolutePath();

        $mediaDirectoryRelativeSubpath = substr($mediaDirectoryPath, strlen($baseDirectoryPath));
        $pubDirectory = $baseDirectory->getRelativePath($pubDirectoryPath);

        if (strpos($mediaDirectoryRelativeSubpath, $pubDirectory) === 0 && strpos($filePath, $pubDirectory) !== 0) {
            $mediaDirectoryRelativeSubpath = substr($mediaDirectoryRelativeSubpath, strlen($pubDirectory));
        }

        return $mediaDirectoryRelativeSubpath;
    }

    /**
     * @param $fileName
     *
     * @return string
     */
    public function getAbsoluteMediaFile($fileName)
    {
        return $this->filesystem->getDirectoryRead(DirectoryList::MEDIA)->getAbsolutePath($fileName);
    }
}
