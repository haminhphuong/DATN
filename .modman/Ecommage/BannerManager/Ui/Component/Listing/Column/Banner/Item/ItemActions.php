<?php
declare(strict_types=1);
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Ecommage\BannerManager\Ui\Component\Listing\Column\Banner\Item;

use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Ui\Component\Listing\Columns\Column;
use Magento\Framework\UrlInterface;

/**
 * Prepare actions column for customer addresses grid
 */
class ItemActions extends Column
{

    /**
     * Url path
     */
    const URL_PATH_DELETE = 'banner/item/delete';

    /**
     * @var UrlInterface
     */
    private $urlBuilder;

    /**
     * @param ContextInterface $context
     * @param UiComponentFactory $uiComponentFactory
     * @param UrlInterface $urlBuilder
     * @param array $components
     * @param array $data
     */
    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        UrlInterface $urlBuilder,
        array $components = [],
        array $data = []
    ) {
        $this->urlBuilder = $urlBuilder;
        parent::__construct($context, $uiComponentFactory, $components, $data);
    }

    /**
     * Prepare Data Source
     *
     * @param array $dataSource
     * @return array
     */
    public function prepareDataSource(array $dataSource): array
    {
        if (isset($dataSource['data']['items'])) {
            foreach ($dataSource['data']['items'] as &$item) {
                $name = $this->getData('name');
                if (isset($item['entity_id'])) {
                    $item[$name]['edit'] = [
                        'callback' => [
                            [
                                'provider' => 'banner_form.areas.banner_items.banner_items'
                                    . '.banner_item_update_modal.update_banner_item_form_loader',
                                'target' => 'destroyInserted',
                            ],
                            [
                                'provider' => 'banner_form.areas.banner_items.banner_items'
                                    . '.banner_item_update_modal',
                                'target' => 'openModal',
                            ],
                            [
                                'provider' => 'banner_form.areas.banner_items.banner_items'
                                    . '.banner_item_update_modal.update_banner_item_form_loader',
                                'target' => 'render',
                                'params' => [
                                    'entity_id' => $item['entity_id'],
                                ],
                            ]
                        ],
                        'href' => '#',
                        'label' => __('Edit'),
                        'hidden' => false,
                    ];

                    $item[$name]['delete'] = [
                        'href' => $this->urlBuilder->getUrl(
                            self::URL_PATH_DELETE,
                            ['banner_id' => $item['banner_id'], 'id' => $item['entity_id']]
                        ),
                        'label' => __('Delete'),
                        'isAjax' => true,
                        'confirm' => [
                            'title' => __('Delete banner item'),
                            'message' => __('Are you sure you want to delete the banner item?')
                        ]
                    ];
                }
            }
        }

        return $dataSource;
    }
}
