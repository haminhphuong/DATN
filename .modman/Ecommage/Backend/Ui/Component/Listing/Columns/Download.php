<?php

namespace Ecommage\Backend\Ui\Component\Listing\Columns;

use Magento\Ui\Component\Listing\Columns\Column;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Framework\UrlInterface;

/**
 * Class SliderActions
 *
 * @package Ecommage\Backend\Ui\Component\Listing\Columns
 */
class Download extends Column
{
    /**
     * @var UrlInterface
     */
    private $urlBuilder;

    /**
     * SliderActions constructor.
     *
     * @param ContextInterface   $context
     * @param UiComponentFactory $uiComponentFactory
     * @param UrlInterface       $urlBuilder
     * @param array              $components
     * @param array              $data
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
     *
     * @return array
     */
    public function prepareDataSource(array $dataSource)
    {
        if (isset($dataSource['data']['items'])) {
            foreach ($dataSource['data']['items'] as &$item) {
                $name = $this->getData('name');
                $item[$name]['download'] = [
                    'href'  => $this->urlBuilder->getUrl(
                        'schedule/product/download',
                        [
                            'product_id' => $item['product_id'],
                            'schedule_id' => $item['schedule_id']
                        ]
                    ),
                    'label' => __('Download')
                ];
            }
        }

        return $dataSource;
    }
}
