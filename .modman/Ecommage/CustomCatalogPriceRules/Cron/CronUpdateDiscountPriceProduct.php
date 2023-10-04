<?php

namespace Ecommage\CustomCatalogPriceRules\Cron;

use Magento\Catalog\Model\Product;
use Magento\Catalog\Model\Product\Attribute\Source\Status;
use Magento\Eav\Api\AttributeRepositoryInterface;
use Magento\Framework\Pricing\PriceCurrencyInterface;

/**
 *
 */
class CronUpdateDiscountPriceProduct
{
    /**
     * @var array
     */
    protected $_options = [] ;


    /**
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Theme\Block\Html\Header\Logo $logo
     * @param \Magento\Store\Model\App\Emulation $emulation
     * @param Product $modelProduct
     * @param \Psr\Log\LoggerInterface $logger
     * @param AttributeRepositoryInterface $attributeRepository
     * @param \Magento\Framework\App\ResourceConnection $resourceConnection
     * @param \Magento\Catalog\Api\ProductRepositoryInterface $productRepository
     * @param \Magento\Eav\Model\Config $eavConfig
     * @param \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $collectionFactory
     * @param PriceCurrencyInterface $priceCurrency
     */
    public function __construct
    (
        \Magento\Backend\App\Action\Context $context,
        \Magento\Theme\Block\Html\Header\Logo $logo,
        \Magento\Store\Model\App\Emulation $emulation,
        \Magento\Catalog\Model\Product $modelProduct ,
        \Psr\Log\LoggerInterface $logger,
        AttributeRepositoryInterface $attributeRepository,
        \Magento\Framework\App\ResourceConnection $resourceConnection,
        \Magento\Catalog\Api\ProductRepositoryInterface $productRepository ,
        \Magento\Eav\Model\Config $eavConfig,
        \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $collectionFactory,
        PriceCurrencyInterface $priceCurrency
    )
    {
        $this->_logo = $logo;
        $this->emulation = $emulation;
        $this->modelProduct = $modelProduct;
        $this->logger = $logger;
        $this->attributeRepository = $attributeRepository;
        $this->resource = $resourceConnection;
        $this->productRepository = $productRepository;
        $this->attribute = $eavConfig;
        $this->priceCurrency = $priceCurrency;
        $this->collectionFactory = $collectionFactory;
    }


    /**
     * @SuppressWarnings(PHPMD.ElseExpression)
     * @return $this
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     * @throws \Magento\Framework\Exception\InputException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\StateException
     */
    public function execute()
    {
        $this->emulation->startEnvironmentEmulation(1, \Magento\Framework\App\Area::AREA_FRONTEND, true); // You can set store id and area

        $collection = $this->collectionFactory->create()
            ->addAttributeToSelect('*')
            ->addAttributeToFilter('status', Status::STATUS_ENABLED);

        $attribute = $this->attributeRepository->get(Product::ENTITY, 'discount_price_range');
        foreach ($collection as $product) {
            $finalPrice = $product->getPriceInfo()->getPrice('final_price')->getAmount()->getValue();
            $price = $product->getPriceInfo()->getPrice('regular_price')->getAmount()->getValue();
            if ($product->getTypeId() == \Magento\ConfigurableProduct\Model\Product\Type\Configurable::TYPE_CODE) {
                $price = $this->priceCurrency->convertAndRound($price);
            }

            if (!empty($price) && !empty($finalPrice)){
                $discount  = $product->getData('discount_price_range');
                $tableName = "catalog_product_entity_varchar";
                if (!$discount) {
                    $discountPrice = $this->setDiscountPrice($price,$finalPrice);
                    try {
                        $data  = [
                            "attribute_id" => $attribute->getAttributeId(),
                            "store_id"     => 0,
                            "entity_id"    => $product->getEntityId(),
                            "value"        => $this->setValueOption($discountPrice)
                        ];

                        $connection = $this->resource->getConnection();
                        $connection->insertOnDuplicate($tableName, $data);
                    } catch (\Exception $e) {
                        $this->logger->error($e->getMessage());
                    }
                } else {
                    try {
                        $discountPrice = $this->setDiscountPrice($price, $finalPrice);
                        if ($discount != $this->setValueOption($discountPrice)) {
                            $connection = $this->resource->getConnection();
                            $connection->update($tableName,
                                [
                                    'value' => $this->setValueOption($discountPrice)
                                ],
                                [
                                    'entity_id IN (?)'    => $product->getEntityId(),
                                    'attribute_id IN (?)' => $attribute->getAttributeId()
                                ]
                            );
                        }
                    } catch (\Exception $e) {
                        $this->logger->error($e->getMessage());
                    }
                }
            }
        }
        $this->emulation->stopEnvironmentEmulation();
        shell_exec('php bin/magento indexer:reset');
        shell_exec('php bin/magento indexer:reindex');

    }

    /**
     * @param $price
     * @param $finalPrice
     *
     * @return float|int
     */
    protected function setDiscountPrice($price, $finalPrice)
    {
        $sale = 0 ;
        if ($finalPrice && $price){
            if ($finalPrice < $price){
                $sale = ($price - $finalPrice) / $price * 100 ;
            }
        }
        return $sale;
    }

    /**
     * @param $id
     *
     * @return array
     */
    protected function setSql($id)
    {
        $connection = $this->resource->getConnection();
        $tableName = $this->resource->getTableName('catalog_product_index_price');
        $sql = $connection->select()->distinct(true)->from(array('catalog' => $tableName), array('*'));
        $sql->where('entity_id = ?',$id);

        return $connection->fetchRow($sql);
    }

    /**
     * @return array
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getOptionAttribute()
    {
        $options = $this->attribute->getAttribute('catalog_product', 'discount_price_range');
        $optionLabel =  $options->getSource()->getAllOptions();
        foreach ($optionLabel as $option){
            if ($option['value'] != ""){
                $this->_options[] = $option['value'];
            }
        }
        return $this->_options;
    }

    /**
     * @param $sale
     *
     * @return mixed|null
     */
    public function setValueOption($sale)
    {
        $values = null ;
        if ($this->getOptionAttribute()){
            foreach ($this->getOptionAttribute() as $value){
                $data = explode('-',$value);
                if ($sale >= $data[0] && $sale <= $data [1]){
                    return $value ;
                }
            }
        }
        return $values;
    }
}
