<?php

namespace Ecommage\SliderCategoryCollection\Ui\Component\Listing\Columns;

use Magento\Ui\Component\Listing\Columns\Column;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Framework\UrlInterface;

/**
 * Class SliderActions
 *
 * @package Ecommage\SliderCategoryCollection\Ui\Component\Listing\Columns
 */
class SliderActions extends Column
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
                $item[$name]['edit'] = [
                    'href'  => $this->urlBuilder->getUrl(
                        'slider_category_collection/slider/edit',
                        ['slider_ctg_id' => $item['slider_ctg_id']]
                    ),
                    'label' => __('Edit')
                ];
                $item[$name]['delete'] = [
                    'href'    => $this->urlBuilder->getUrl(
                        'slider_category_collection/slider/delete',
                        ['slider_ctg_id' => $item['slider_ctg_id']]
                    ),
                    'label'   => __('Delete'),
                    'confirm' => [
                        'title'   => __('Delete ${ $.$data.name }'),
                        'message' => __('Are you sure you wan\'t to delete a ${ $.$data.name } record?')
                    ]
                ];
            }
        }

        return $dataSource;
    }
}
