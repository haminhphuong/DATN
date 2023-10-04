<?php

declare(strict_types=1);

namespace Ecommage\CustomerReview\Model\Review;

use Ecommage\CustomerReview\Model\ResourceModel\Review\CollectionFactory;
use Magento\Framework\App\Request\DataPersistorInterface;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Ui\DataProvider\AbstractDataProvider;

/**
 * Class DataProvider
 *
 * @package Ecommage\CustomerReview\Model\Review
 */
class DataProvider extends AbstractDataProvider
{
    const IMAGE_PATH = 'ecommage/tmp/customer_review/images/';
    const VIDEO_PATH = 'ecommage/tmp/customer_review/videos/';
    protected $collection;
    protected $dataPersistor;
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
            if (!empty($item->getPicture())) {
                $fullData = $this->loadedData;
                $this->loadedData[$item->getId()] = array_merge($fullData[$item->getId()], $this->convertStringToArray('picture',self::IMAGE_PATH, $item->getPicture()));
            }
            if (!empty($item->getVideo())) {
                $fullData = $this->loadedData;
                $this->loadedData[$item->getId()] = array_merge($fullData[$item->getId()], $this->convertStringToArray('video',self::VIDEO_PATH, $item->getVideo()));
            }
            if (!empty($item->getImage())) {
                $fullData = $this->loadedData;
                $this->loadedData[$item->getId()] = array_merge($fullData[$item->getId()], $this->convertStringToArray('image',self::IMAGE_PATH, $item->getImage()));
            }
        }
        $data = $this->dataPersistor->get('ecommage_customer_review');
        if (!empty($data)) {
            $model = $this->collection->getNewEmptyItem();
            $model->setData($data);
            $this->loadedData[$model->getId()] = $model->getData();
            $this->dataPersistor->clear('ecommage_customer_review');
        }
        return $this->loadedData;
    }

    public function convertStringToArray($fieldName,$path, $fileName)
    {
        $urlFile = [];
        $urlFile[$fieldName][0]['name'] = $fileName;
        $urlFile[$fieldName][0]['url'] = $this->storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA).$path.$fileName;
        return $urlFile;
    }
}
