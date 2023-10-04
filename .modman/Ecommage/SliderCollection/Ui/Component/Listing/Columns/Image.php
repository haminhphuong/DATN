<?php

namespace Ecommage\SliderCollection\Ui\Component\Listing\Columns;

use Magento\Framework\UrlInterface;
use Magento\Framework\View\Asset\Repository;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Ui\Component\Listing\Columns\Column;
use Magento\Store\Model\StoreManagerInterface;

/**
 * Class Picture
 *
 * @package Ecommage\SliderCollection\Ui\Component\Listing\Columns
 */
class Image extends Column
{

    /**
     * @var Repository
     */
    protected $viewFileUrl;
    /**
     * @var UrlInterface
     */
    protected $urlBuilder;
    /**
     * @var StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * Picture constructor.
     *
     * @param ContextInterface      $context
     * @param UiComponentFactory    $uiComponentFactory
     * @param UrlInterface          $urlBuilder
     * @param Repository            $viewFileUrl
     * @param StoreManagerInterface $storeManager
     * @param array                 $components
     * @param array                 $data
     */
    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        UrlInterface $urlBuilder,
        Repository $viewFileUrl,
        StoreManagerInterface $storeManager,
        array $components = [],
        array $data = []
    ) {
        parent::__construct($context, $uiComponentFactory, $components, $data);
        $this->urlBuilder    = $urlBuilder;
        $this->viewFileUrl   = $viewFileUrl;
        $this->_storeManager = $storeManager;
    }

    /**
     * Prepare Data Source
     *
     * @param array $dataSource
     *
     * @return array
     */
    public function prepareDataSource(array $dataSource)
    {
        if (isset($dataSource['data']['items'])) {
            $fieldName = $this->getData('name');
            foreach ($dataSource['data']['items'] as & $item) {
                $mediaPath                      = $this->_storeManager->getStore()->getBaseUrl(UrlInterface::URL_TYPE_MEDIA);
                $imagePath                      = !empty($item[$fieldName]) ? $mediaPath . $item[$fieldName]
                    : $this->viewFileUrl->getUrl(
                        'Ecommage_SliderCollection::images/no-image.png'
                    );
                $item[$fieldName . '_src']      = $imagePath;
                $item[$fieldName . '_orig_src'] = $imagePath;
                $item[$fieldName . '_alt']      = $item['title'];
                $item[$fieldName . '_link'] = $this->urlBuilder->getUrl(
                    'slider_collection/slider/edit',
                    ['slider_id' => $item['slider_id']]
                );
            }
        }

        return $dataSource;
    }
}
