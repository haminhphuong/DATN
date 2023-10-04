<?php

namespace Ecommage\CustomCatalogPriceRules\Console;

use Magento\Catalog\Model\Product;
use Magento\Catalog\Model\Product\Attribute\Source\Status;
use Magento\Catalog\Model\Product\Visibility;
use Magento\Framework\Pricing\PriceCurrencyInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Magento\Framework\App\State;
use Magento\Eav\Api\AttributeRepositoryInterface;
use Magento\ConfigurableProduct\Model\ResourceModel\Product\Type\Configurable;
use Magento\Bundle\Api\ProductLinkManagementInterface;
/**
 *
 */
class UpdateDiscountPriceRules extends Command
{
    /**
     * @var array
     */
    protected $_options = [] ;

    /**
     * @param \Magento\Framework\App\ResourceConnection                      $resource
     * @param AttributeRepositoryInterface                                   $attributeRepository
     * @param State                                                          $state
     * @param \Magento\Framework\App\ResourceConnection                      $resourceConnection
     * @param \Magento\Catalog\Api\ProductRepositoryInterface                $productRepository
     * @param \Magento\Eav\Model\Config                                      $eavConfig
     * @param \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $collectionFactory
     * @param string|null                                                    $name
     */
    public function __construct
    (
        PriceCurrencyInterface $priceCurrency,
        \Psr\Log\LoggerInterface $logger,
        \Magento\Framework\App\ResourceConnection $resource,
        AttributeRepositoryInterface $attributeRepository,
        State $state,
        \Magento\Catalog\Model\Product $modelProduct ,
        \Magento\Framework\App\ResourceConnection $resourceConnection,
        \Magento\Catalog\Api\ProductRepositoryInterface $productRepository ,
        \Magento\Eav\Model\Config $eavConfig,
        \Magento\Framework\Stdlib\DateTime\TimezoneInterface $timezone,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $collectionFactory,
        string $name = null
    )
    {
        $this->storeManager = $storeManager;
        $this->timezone = $timezone;
        $this->priceCurrency = $priceCurrency;
        $this->logger = $logger;
        $this->resource = $resource;
        $this->attributeRepository = $attributeRepository;
        $this->state = $state;
        $this->resourceConnection = $resourceConnection;
        $this->productRepository = $productRepository;
        $this->attribute = $eavConfig;
        $this->collectionFactory = $collectionFactory;
        parent::__construct($name);
    }

    /**
     * @return void
     */
    protected function configure()
    {
        $this->setName('ecommage:update:discountPrice');
        $this->setDescription('This is my console command run in update discount price!');
        parent::configure();
    }

    /**
     * @param InputInterface  $input
     * @param OutputInterface $output
     * @SuppressWarnings(PHPMD.ElseExpression)
     * @return void
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->state->setAreaCode(\Magento\Framework\App\Area::AREA_ADMINHTML);

        $output->writeln("Start Update Discount Price For All Products...");
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
                    try {
                        $discountPrice = $this->setDiscountPrice($price,$finalPrice);
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
        $output->writeln("Finished!");
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
        $connection = $this->resourceConnection->getConnection();
        $tableName = $this->resourceConnection->getTableName('catalog_product_index_price');
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

    /**
     * @param string|null $date
     *
     * @return string
     */
    private function getCompareDate($date = null,$storeId = null)
    {
        return $this->timezone->scopeDate(
            $this->storeManager->getStore($storeId),
            $date
        )->format('Y-m-d');
    }

}
