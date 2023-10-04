<?php

declare(strict_types=1);

namespace Ecommage\SliderCategoryCollection\Model\Slider;

use Ecommage\SliderCategoryCollection\Model\ResourceModel\Slider\CollectionFactory;
use Magento\Framework\App\Request\DataPersistorInterface;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Ui\DataProvider\AbstractDataProvider;

/**
 * Class DataProvider
 *
 * @package Ecommage\SliderCategoryCollection\Model\Slider
 */
class DataProvider extends AbstractDataProvider
{

    /**
     * @var \Ecommage\SliderCategoryCollection\Model\ResourceModel\Slider\Collection
     */
    protected $collection;
    /**
     * @var DataPersistorInterface
     */
    protected $dataPersistor;
    /**
     * @var
     */
    protected $loadedData;
    /**
     * @var StoreManagerInterface
     */
    protected $storeManager;

    /**
     * DataProvider constructor.
     *
     * @param string                 $name
     * @param string                 $primaryFieldName
     * @param string                 $requestFieldName
     * @param CollectionFactory      $collectionFactory
     * @param DataPersistorInterface $dataPersistor
     * @param StoreManagerInterface  $storeManager
     * @param array                  $meta
     * @param array                  $data
     */
    public function __construct(
        $name,
        $primaryFieldName,
        $requestFieldName,
        CollectionFactory $collectionFactory,
        DataPersistorInterface $dataPersistor,
        StoreManagerInterface $storeManager,
        array $meta = [],
        array $data = []
    )
    {
        $this->collection    = $collectionFactory->create();
        $this->dataPersistor = $dataPersistor;
        $this->storeManager  = $storeManager;
        parent::__construct($name, $primaryFieldName, $requestFieldName, $meta, $data);
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
            $this->loadedData[$item->getId()] = $item->getData();
            if (!empty($item->getImage())) {
                $fullData = $this->loadedData;
                $this->loadedData[$item->getId()] = array_merge($fullData[$item->getId()], $this->convertStringToArray('image', $item->getImage()));
            }
            if (!empty($item->getImageWatch())) {
                $fullData = $this->loadedData;
                $this->loadedData[$item->getId()] = array_merge($fullData[$item->getId()], $this->convertStringToArray('image_watch', $item->getImageWatch()));
            }
        }
        $data = $this->dataPersistor->get('ecommage_category_slider');
        if (!empty($data)) {
            $model = $this->collection->getNewEmptyItem();
            $model->setData($data);
            $this->loadedData[$model->getId()] = $model->getData();
            $this->dataPersistor->clear('ecommage_category_slider');
        }
        return $this->loadedData;
    }

    /**
     * @param $fieldName
     * @param $fileName
     * @return array
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function convertStringToArray($fieldName, $fileName)
    {
        $urlFile = [];
        $urlFile[$fieldName][0]['name'] = $fileName;
        $urlFile[$fieldName][0]['url'] = $this->storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA).$fileName;
        return $urlFile;
    }
}
