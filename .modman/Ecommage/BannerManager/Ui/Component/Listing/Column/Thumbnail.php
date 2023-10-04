<?php
/**
 * Copyright © Thomas Nguyen, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Ecommage\BannerManager\Ui\Component\Listing\Column;

use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Framework\View\Element\UiComponent\ContextInterface;

/**
 * Class Thumbnail
 *
 * @api
 * @since 100.0.2
 */
class Thumbnail extends \Magento\Ui\Component\Listing\Columns\Column
{
    const NAME = 'title';

    const ALT_FIELD = 'title';

    protected $imageHelper;

    /**
     * Thumbnail constructor.
     *
     * @param ContextInterface                $context
     * @param UiComponentFactory              $uiComponentFactory
     * @param \Ecommage\BannerManager\Helper\Image $imageHelper
     * @param array                           $components
     * @param array                           $data
     */
    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        \Ecommage\BannerManager\Helper\Image $imageHelper,
        array $components = [],
        array $data = []
    ) {
        parent::__construct($context, $uiComponentFactory, $components, $data);
        $this->imageHelper = $imageHelper;
    }

    /**
     * Prepare Data Source
     *
     * @param array $dataSource
     * @return array
     */
    public function prepareDataSource(array $dataSource)
    {
        if (isset($dataSource['data']['items'])) {
            $fieldName = $this->getData('name');
            foreach ($dataSource['data']['items'] as & $item) {
                $urlImage = $this->imageHelper->getImageUrl($item[$fieldName]);
                $item[$fieldName . '_src'] = $urlImage;
                $item[$fieldName . '_alt'] = $this->getAlt($item);
                $item[$fieldName . '_link'] = $this->getUrl('banner/index/edit', []);
                $item[$fieldName . '_orig_src'] = $urlImage;
            }
        }

        return $dataSource;
    }

    /**
     * Get Alt
     *
     * @param array $row
     *
     * @return null|string
     */
    protected function getAlt($row)
    {
        $altField = $this->getData('config/altField') ?: self::ALT_FIELD;
        return $row[$altField] ?? null;
    }

    /**
     * @param       $path
     * @param array $params
     *
     * @return string
     */
    protected function getUrl($path, $params = [])
    {
        return $this->context->getUrl($path, $params);
    }
}
