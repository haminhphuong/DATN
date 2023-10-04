<?php

namespace Ecommage\BannerManager\Model\Banner;

use Ecommage\BannerManager\Model\ResourceModel\Banner\CollectionFactory;
use Magento\Framework\App\Request\DataPersistorInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Ecommage\BannerManager\Model\Banner;

/**
 * Class DataProvider
 */
class DataProvider extends \Magento\Ui\DataProvider\AbstractDataProvider
{
    /**
     * @var array
     */
    protected $templates = [];
    /**
     * @var
     */
    protected $collection;
    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $scopeConfig;
    /**
     * @var DataPersistorInterface
     */
    protected $dataPersistor;
    /**
     * @var array
     */
    protected $loadedData = [];

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
        ScopeConfigInterface $scopeConfig,
        array $meta = [],
        array $data = []
    ) {
        $this->scopeConfig   = $scopeConfig;
        $this->dataPersistor = $dataPersistor;
        $this->collection    = $collectionFactory->create();
        parent::__construct($name, $primaryFieldName, $requestFieldName, $meta, $data);
    }

    /**
     * Get data
     *
     * @return array
     */
    public function getData(): array
    {
        if (!empty($this->loadedData)) {
            return $this->loadedData;
        }

        $banners = $this->collection->getItems();
        /** @var \Ecommage\BannerManager\Model\Banner $banner */
        foreach ($banners as $banner) {
            $this->loadedData[$banner->getId()] = $this->prepareData($banner);
        }

        $data = $this->dataPersistor->get('banner_entity');
        if (!empty($data)) {
            $this->loadedData[$banner->getId()] = $this->prepareData($data);;
            $this->dataPersistor->clear('banner_entity');
        }

        return $this->loadedData;
    }

    /**
     *  Prepares Data
     *
     * @param $data
     *
     * @return array
     */
    private function prepareData($data): array
    {
        $bannerData = $data;
        if (!($data instanceof \Ecommage\BannerManager\Model\Banner)) {
            $bannerData = $this->collection->getNewEmptyItem();
            $bannerData->setData($data);
        }

        $options = $bannerData->getOptions();
        if ($templateId = $this->getTemplateId()) {
            $options = $this->getTemplate($templateId);
        }
        if (empty($options)) {
            $options = $this->getDefaultOptions();
        }

        $this->addDefaultOptions($options);
        $result[Banner::FORM_GENERAL] = $bannerData->getData();
        $result[Banner::FORM_OPTIONS] = $options;
        return $result;
    }

    /**
     * @return mixed
     */
    protected function getDefaultOptions()
    {
        return $this->scopeConfig->getValue('ecommage/banner/options');
    }

    /**
     * @param $options
     */
    protected function addDefaultOptions(&$options)
    {
        $defaultOptions = $this->getDefaultOptions();
        foreach ($defaultOptions as $key => $value) {
            if (!isset($options[$key])) {
                $options[$key] = $value;
            }
        }

        return $options;
    }

    /**
     * @return string|null
     */
    public function getTemplateId()
    {
        $templateId = $this->dataPersistor->get('banner_slider_template');
        if (!empty($templateId)) {
            return $templateId;
        }

        return null;
    }

    /**
     * @param $id
     *
     * @return array|mixed
     */
    public function getTemplate($id)
    {
        if (empty($this->templates)) {
            $this->initTemplates();
        }

        return $this->templates[$id] ?? [];
    }

    /**
     * @return $this
     */
    public function initTemplates()
    {
        $this->getBasicTemplate()
             ->getFullWidthCarouselTemplate()
             ->getBottomThumbnailsTemplate()
             ->getVideoTemplate()
             ->getVerticalThumbnailsTemplate();
        return $this;
    }

    /**
     * @return array
     */
    private function getBasicTemplate()
    {
        $this->templates[1] = [
            'width'            => 960,
            'height'           => 500,
            'arrows'           => true,
            'buttons'          => false,
            'waitForLayers'    => true,
            'thumbnailWidth'   => 200,
            'thumbnailHeight'  => 100,
            'thumbnailPointer' => true,
            'autoplay'         => false,
            'autoScaleLayers'  => false,
            'breakpoints'      => json_encode(
                [
                    [
                        500 => [
                            'thumbnailWidth'  => 120,
                            'thumbnailHeight' => 50,
                        ]
                    ]
                ]
            ),
        ];
        return $this;
    }

    /**
     * @return array
     */
    private function getFullWidthCarouselTemplate()
    {
        $this->templates[2] = [
            'width'         => 500,
            'height'        => 300,
            'autoplay'      => false,
            'visibleSize'   => '100%',
            'forceSize'     => 'fullWidth',
            'autoSlideSize' => true,
        ];
        return $this;
    }

    /**
     * @return array
     */
    private function getBottomThumbnailsTemplate()
    {
        $this->templates[3] = [
            'width'           => 960,
            'height'          => 500,
            'fade'            => true,
            'arrows'          => true,
            'buttons'         => false,
            'fullScreen'      => true,
            'shuffle'         => true,
            'smallSize'       => 500,
            'mediumSize'      => 1000,
            'largeSize'       => 3000,
            'thumbnailArrows' => true,
            'autoplay'        => false,
        ];
        return $this;
    }

    /**
     * @return array
     */
    private function getVideoTemplate()
    {
        $this->templates[4] = [
            'width'      => 960,
            'height'     => 400,
            'autoHeight' => true,
            'fade'       => true,
            'updateHash' => true,
        ];
        return $this;
    }

    /**
     * @return array
     */
    private function getVerticalThumbnailsTemplate()
    {
        $this->templates[5] = [
            'width'              => 670,
            'height'             => 500,
            'orientation'        => 'vertical',
            'loop'               => false,
            'arrows'             => true,
            'buttons'            => false,
            'thumbnailsPosition' => 'right',
            'thumbnailPointer'   => true,
            'thumbnailWidth'     => '290',
            'breakpoints'        => json_encode(
                [
                    500 => [
                        'thumbnailsPosition' => 'bottom',
                        'thumbnailWidth'     => 120,
                        'thumbnailHeight'    => 50,
                    ],
                    800 => [
                        'thumbnailsPosition' => 'bottom',
                        'thumbnailWidth'     => 270,
                        'thumbnailHeight'    => 100,
                    ]
                ]
            ),
        ];
        return $this;
    }
}
