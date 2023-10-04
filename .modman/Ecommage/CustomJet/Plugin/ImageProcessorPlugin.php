<?php

namespace Ecommage\CustomJet\Plugin;

use Amasty\JetTheme\Model\ImageProcessor;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Filesystem\Io\File;
use Magento\Framework\Image\AdapterFactory;

class ImageProcessorPlugin
{

    /**
     * @var File
     */
    protected $ioFile;

    /**
     * @var AdapterFactory
     */
    protected $adapterFactory;

    /**
     * @param File           $ioFile
     * @param AdapterFactory $adapterFactory
     */
    public function __construct(
        File           $ioFile,
        AdapterFactory $adapterFactory
    ) {
        $this->ioFile         = $ioFile;
        $this->adapterFactory = $adapterFactory;

    }

    /**
     * @param ImageProcessor $subject
     * @param callable       $proceed
     * @param string         $filePath
     * @param string         $resizedFilePath
     * @param int            $width
     * @param int            $height
     *
     * @return void
     * @throws LocalizedException
     * @SuppressWarnings(PHPMD)
     */
    public function aroundResizeImage(
        ImageProcessor $subject,
        callable       $proceed,
        string         $filePath,
        string         $resizedFilePath,
        int            $width,
        int            $height
    ) {
        if (!$filePath || !$this->ioFile->fileExists($filePath)) {
            throw new LocalizedException(__('File is not exist!'));
        }

        $imageResize = $this->adapterFactory->create();
        $imageResize->open($filePath);
        $imageResize->constrainOnly(true);
        $imageResize->keepTransparency(true);
        $imageResize->keepFrame(false);
        $imageResize->keepAspectRatio(true);
        $imageResize->resize($width, $height);
        //save image
        $imageResize->save($resizedFilePath);
    }
}
