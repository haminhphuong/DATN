<?php
namespace Ecommage\WidgetCatalogPriceRule\Helper;

class CategoryData {

    protected $storeManager;
    protected $categoryCollection;

    public function __construct(
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Catalog\Model\ResourceModel\Category\CollectionFactory $categoryCollection
    ) {
        $this->storeManager = $storeManager;
        $this->categoryCollection = $categoryCollection;
    }

    public function getCategoryCollection(){
        $collection = $this->categoryCollection->create()
            ->addAttributeToSelect('*')
            ->setStore($this->storeManager->getStore())
            //->addAttributeToFilter('attribute_code', '1')
            ->addAttributeToFilter('is_active','1');
        return $collection;
    }
}
