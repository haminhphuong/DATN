<?php
declare(strict_types=1);

namespace Ecommage\BookingViewProduct\Ui\Component\Listing\Columns;

use Magento\Catalog\Model\ProductFactory;
use Magento\Ui\Component\Listing\Columns\Column;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Framework\UrlInterface;

class ProductActions extends Column
{
    /** Url Path */
    const PRODUCT_URL_PATH_EDIT = 'catalog/product/edit';

    /**
     * @var UrlInterface
     */
    private $urlBuilder;
    /**
     * @var ProductFactory
     */
    protected $productFactory;

    /**
     * @param ContextInterface $context
     * @param ProductFactory $productFactory
     * @param UiComponentFactory $uiComponentFactory
     * @param UrlInterface $urlBuilder
     * @param array $components
     * @param array $data
     */
    public function __construct(
        ContextInterface                      $context,
        ProductFactory $productFactory,
        UiComponentFactory                    $uiComponentFactory,
        UrlInterface                          $urlBuilder,
        array                                 $components = [],
        array                                 $data = []
    )
    {
        parent::__construct($context, $uiComponentFactory, $components, $data);
        $this->urlBuilder = $urlBuilder;
        $this->productFactory = $productFactory;
    }

    /**
     * Prepare Data Source
     *
     * @param array $dataSource
     *
     * @return array
     */
    public function prepareDataSource(array $dataSource): array
    {
        if (isset($dataSource['data']['items'])) {
            foreach ($dataSource['data']['items'] as & $item) {
                $name = $this->getData('name');
                if (isset($item['product_id'])) {
                    $item[$name] = html_entity_decode('<a href="' . $this->urlBuilder->getUrl(self::PRODUCT_URL_PATH_EDIT, ['id' => $item['product_id']]) . '">' . $this->getProductName($item['product_id']) . '</a>');// @codingStandardsIgnoreLine
                }
            }
        }
        return $dataSource;
    }

    /**
     * @param $productId
     * @return string
     */
    public function getProductName($productId)
    {
        $productModel = $this->productFactory->create()
            ->load($productId);
        return $productModel->getName();
    }
}
