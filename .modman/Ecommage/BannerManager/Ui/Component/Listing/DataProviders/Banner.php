<?php
namespace Ecommage\BannerManager\Ui\Component\Listing\DataProviders;

/**
 * Class Banner
 *
 * @package Ecommage\BannerManager\Ui\Component\Listing\DataProviders\Banners
 */
class Banner extends \Magento\Ui\DataProvider\AbstractDataProvider
{
    public function __construct(
        $name,
        $primaryFieldName,
        $requestFieldName,
        \Ecommage\BannerManager\Model\ResourceModel\Banner\CollectionFactory $collectionFactory,
        array $meta = [],
        array $data = []
    ) {
        parent::__construct($name, $primaryFieldName, $requestFieldName, $meta, $data);
        $this->collection = $collectionFactory->create();
    }
}
