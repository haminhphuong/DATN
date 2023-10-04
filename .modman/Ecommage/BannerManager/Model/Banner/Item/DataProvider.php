<?php
/**
 * Copyright © Thomas Nguyen, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Ecommage\BannerManager\Model\Banner\Item;

use Magento\Framework\Filesystem;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Ui\DataProvider\AbstractDataProvider;
use Ecommage\BannerManager\Model\Banner\FileInfo;
use Ecommage\BannerManager\Model\Banner\Item;
use Magento\Framework\App\ObjectManager;
use Ecommage\BannerManager\Model\ResourceModel\Banner\Item\Collection;
use Ecommage\BannerManager\Model\ResourceModel\Banner\Item\CollectionFactory;
use Magento\Framework\App\Request\DataPersistorInterface;

/**
 * Class DataProvider
 */
class DataProvider extends AbstractDataProvider
{
    /**
     * @var Collection
     */
    protected $collection;

    /**
     * @var DataPersistorInterface
     */
    protected $dataPersistor;

    /**
     * @var array
     */
    protected $loadedData;

    /**
     * @var Filesystem
     */
    private $fileInfo;

    /**
     * @var ContextInterface
     */
    private $context;

    /**
     * @param string                 $name
     * @param string                 $primaryFieldName
     * @param string                 $requestFieldName
     * @param CollectionFactory      $collectionFactory
     * @param DataPersistorInterface $dataPersistor
     * @param array                  $meta
     * @param array                  $data
     */
    public function __construct(
        $name,
        $primaryFieldName,
        $requestFieldName,
        CollectionFactory $collectionFactory,
        DataPersistorInterface $dataPersistor,
        ContextInterface $context,
        array $meta = [],
        array $data = []
    ) {
        $this->context       = $context;
        $this->collection    = $collectionFactory->create();
        $this->dataPersistor = $dataPersistor;
        parent::__construct($name, $primaryFieldName, $requestFieldName, $meta, $data);
        $this->meta = $this->prepareMeta($this->meta);
    }

    /**
     * Prepares Meta
     *
     * @param array $meta
     *
     * @return array
     */
    public function prepareMeta(array $meta)
    {
        return $meta;
    }

    /**
     * Get data
     *
     * @return array
     */
    public function getData()
    {
        if (isset($this->loadedData)) {
            return $this->loadedData;
        }

        $items = $this->collection->getItems();
        foreach ($items as $item) {
            $this->getInfo($item, Item::IMAGE_DESKTOP);
            $this->getInfo($item, Item::IMAGE_MOBILE);
            $this->loadedData[$item->getId()] = $this->getItemData($item);
        }

        $data = $this->dataPersistor->get('banner_item');
        if (!empty($data)) {
            $item = $this->collection->getNewEmptyItem();
            $item->setData($data);
            $this->loadedData[$item->getId()] = $this->getItemData($item);
            $this->dataPersistor->clear('banner_item');
        }

        return $this->loadedData;
    }

    /**
     * @param $item
     *
     * @return mixed
     */
    private function getItemData($item)
    {
        $item->getDataUsingMethod(Item::OPTIONS);
        return $item->getData();
    }

    /**
     * @param $item
     * @param $attributeCode
     *
     * @return array
     * @SuppressWarnings(PHPMD.ElseExpression)
     */
    protected function getInfo($item, $attributeCode)
    {
        $result   = [];
        $fileInfo = $this->getFileInfo();
        $fileName = $item->getData($attributeCode);
        if (!empty($fileName) && $fileInfo->isExist($fileName)) {
            $stat = $fileInfo->getStat($fileName);
            $mime = $fileInfo->getMimeType($fileName);
            // phpcs:ignore Magento2.Functions.DiscouragedFunction
            $result[0]['name'] = basename($fileName);
            if ($fileInfo->isBeginsWithMediaDirectoryPath($fileName)) {
                $result[0]['url'] = $fileName;
            } else {
                $result[0]['url'] = $item->getImageUrl($attributeCode);
            }

            $result[0]['size'] = isset($stat) ? $stat['size'] : 0;
            $result[0]['type'] = $mime;
        }

        $item->setData($attributeCode, $result);
        return $result;
    }

    /**
     * Get FileInfo instance
     *
     * @return FileInfo
     *
     * @deprecated 102.0.0
     */
    private function getFileInfo(): FileInfo
    {
        if ($this->fileInfo === null) {
            $this->fileInfo = ObjectManager::getInstance()->get(FileInfo::class);
        }
        return $this->fileInfo;
    }
}
