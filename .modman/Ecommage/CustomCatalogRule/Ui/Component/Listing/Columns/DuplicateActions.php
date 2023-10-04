<?php

namespace Ecommage\CustomCatalogRule\Ui\Component\Listing\Columns;

use Magento\Ui\Component\Listing\Columns\Column;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Framework\UrlInterface;

/**
 * Class DuplicateActions
 *
 * @package Ecommage\CustomCatalogRule\Ui\Component\Listing\Columns
 */
class DuplicateActions extends Column
{
    /**
     * @var UrlInterface
     */
    private $urlBuilder;

    /**
     * ReviewActions constructor.
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
                $item[$name]['duplicate'] = [
                    'href'  => $this->urlBuilder->getUrl(
                        'custom_catalog_rule/promo_catalog/duplicate',
                        ['rule_id' => $item['rule_id']]
                    ),
                    'label' => __('Duplicate')
                ];
                $item[$name]['edit'] = [
                    'href'  => $this->urlBuilder->getUrl(
                        'catalog_rule/promo_catalog/edit',
                        ['id' => $item['rule_id']]
                    ),
                    'label' => __('Edit')
                ];
            }
        }

        return $dataSource;
    }
}
