<?php

namespace Ecommage\BannerManager\Ui\Component\Listing\Column\Banner;

use Ecommage\BannerManager\Model\Banner;
use Magento\Ui\Component\Listing\Columns\Column;

/**
 * Class PageActions
 *
 * @package Ecommage\BannerManager\Ui\Component\Listing\Column\Banner
 */
class BannerActions extends Column
{
    /**
     * Url path
     */
    const URL_PATH_EDIT = 'banner/index/edit';
    const URL_PATH_DELETE = 'banner/index/delete';

    /**
     * @param array $dataSource
     *
     * @return array
     */
    public function prepareDataSource(array $dataSource)
    {
        if (isset($dataSource['data']['items'])) {
            $name = $this->getData('name');
            $indexField = $this->getConfig('indexField');
            foreach ($dataSource['data']['items'] as & $item) {
                if (isset($item[$indexField])) {
                    $id = $item[$indexField];
                    $title = $item[Banner::TITLE];
                    $item[$name] = [
                        'edit' => [
                            'href' => $this->context->getUrl(
                                static::URL_PATH_EDIT,
                                [
                                    $indexField => $id,
                                ]
                            ),
                            'label' => __('Edit'),
                            '__disableTmpl' => true,
                        ],
                        'delete' => [
                            'href' => $this->context->getUrl(
                                static::URL_PATH_DELETE,
                                [
                                    $indexField => $id,
                                ]
                            ),
                            'label' => __('Delete'),
                            'confirm' => [
                                'title' => __('Delete %1', $title),
                                'message' => __('Are you sure you want to delete a %1 record?', $title),
                            ],
                            'post' => true,
                            '__disableTmpl' => true,
                        ],
                    ];
                }
            }
        }

        return $dataSource;
    }

    /**
     * @param $key
     *
     * @return mixed|null
     */
    protected function getConfig($key, $default = null)
    {
        $config = $this->getConfiguration();
        return $config[$key] ?? $default;
    }
}
