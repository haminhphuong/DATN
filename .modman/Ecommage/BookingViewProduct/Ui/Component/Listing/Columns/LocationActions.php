<?php
declare(strict_types=1);

namespace Ecommage\BookingViewProduct\Ui\Component\Listing\Columns;

use Amasty\Storelocator\Model\LocationFactory;
use Magento\Ui\Component\Listing\Columns\Column;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Framework\UrlInterface;

class LocationActions extends Column
{
    /** Url Path */
    const PRODUCT_URL_PATH_EDIT = 'amasty_storelocator/location/edit';

    /**
     * @var UrlInterface
     */
    private $urlBuilder;
    /**
     * @var LocationFactory
     */
    protected $_locationFactory;

    /**
     * @param ContextInterface $context
     * @param LocationFactory $locationFactory
     * @param UiComponentFactory $uiComponentFactory
     * @param UrlInterface $urlBuilder
     * @param array $components
     * @param array $data
     */
    public function __construct(
        ContextInterface                           $context,
        LocationFactory $locationFactory,
        UiComponentFactory                         $uiComponentFactory,
        UrlInterface                               $urlBuilder,
        array                                      $components = [],
        array                                      $data = []
    )
    {
        parent::__construct($context, $uiComponentFactory, $components, $data);
        $this->urlBuilder = $urlBuilder;
        $this->_locationFactory = $locationFactory;
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
                if (isset($item['location_id'])) {
                    $item[$name] = html_entity_decode('<a href="' . $this->urlBuilder->getUrl(self::PRODUCT_URL_PATH_EDIT, ['id' => $item['location_id']]) . '">' . $this->getLocationName($item['location_id']) . '</a>');// @codingStandardsIgnoreLine
                }
            }
        }
        return $dataSource;
    }

    /**
     * @param $locationId
     * @return string
     */
    public function getLocationName($locationId)
    {
        $productModel = $this->_locationFactory->create()
            ->load($locationId);
        return $productModel->getName();
    }
}
