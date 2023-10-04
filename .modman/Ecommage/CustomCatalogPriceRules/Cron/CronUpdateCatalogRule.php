<?php

namespace Ecommage\CustomCatalogPriceRules\Cron;

use Magento\Catalog\Model\Product;
use Magento\Eav\Api\AttributeRepositoryInterface;

class CronUpdateCatalogRule
{

    const TABLE_NAME = 'catalog_product_entity_int';

    /**
     * @var \Magento\CatalogRule\Model\RuleFactory
     */
    private $ruleFactory;

    /**
     * @var \Magento\Store\Model\App\Emulation
     */
    private $emulation;

    /**
     * @var \Psr\Log\LoggerInterface
     */
    private $logger;

    /**
     * @var \Magento\Framework\Stdlib\DateTime\DateTime
     */
    private $date;

    /**
     * @var AttributeRepositoryInterface
     */
    private $attributeRepository;

    /**
     * @var \Magento\Framework\App\ResourceConnection
     */
    private $resource;

    /**
     * @param \Magento\CatalogRule\Model\RuleFactory $ruleFactory
     * @param \Magento\Store\Model\App\Emulation $emulation
     * @param \Psr\Log\LoggerInterface $logger
     * @param \Magento\Framework\Stdlib\DateTime\DateTime $date
     * @param AttributeRepositoryInterface $attributeRepository
     * @param \Magento\Framework\App\ResourceConnection $resourceConnection
     */
    public function __construct
    (
        \Magento\CatalogRule\Model\RuleFactory $ruleFactory,
        \Magento\Store\Model\App\Emulation $emulation,
        \Psr\Log\LoggerInterface $logger,
        \Magento\Framework\Stdlib\DateTime\DateTime $date,
        AttributeRepositoryInterface $attributeRepository,
        \Magento\Framework\App\ResourceConnection $resourceConnection
    )
    {
        $this->ruleFactory = $ruleFactory;
        $this->emulation = $emulation;
        $this->logger = $logger;
        $this->date = $date;
        $this->attributeRepository = $attributeRepository;
        $this->resource = $resourceConnection;
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
        $catalogRuleCollection = $this->ruleFactory->create()->getCollection()
            ->addFieldToFilter('simple_action', 'to_fixed')
            ->addFieldToFilter('is_active', 1)
            ->addIsActiveFilter(1);
        foreach ($catalogRuleCollection as $catalogRule) {
            /** @var \Magento\CatalogRule\Model\Rule  $catalogRule  */
                $productIdsAccToRule = $catalogRule->getMatchingProductIds();
            foreach ($productIdsAccToRule as $productId => $ruleProductArray) {
                    $fromDate = strtotime($catalogRule->getFromDate());
                    $toDate = strtotime($catalogRule->getToDate());
                    $this->insertData($fromDate,$toDate,$productId,$catalogRule->getId());
            }
        }

        $this->emulation->stopEnvironmentEmulation();
        shell_exec('php bin/magento indexer:reset');
        shell_exec('php bin/magento indexer:reindex');
    }

    /**
     * @param $fromDate
     * @param $toDate
     * @return int
     */
    public function setCompareDate($fromDate, $toDate)
    {
        $current = strtotime($this->date->gmtDate('Y-m-d'));

        if ($current > $toDate && $toDate){
            return 1;
        }
        if ($current == $fromDate){
            return 2;
        }
        return  3;
    }

    /**
     * @param $fromDate
     * @param $toDate
     * @param $productId
     * @param $ruleId
     * @return $this
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function insertData($fromDate, $toDate, $productId, $ruleId)
    {
        $attribute = $this->attributeRepository->get(Product::ENTITY, 'catalog_rule');
        switch ($this->setCompareDate($fromDate,$toDate)){
            case 1 :
                try {
                    $connection = $this->resource->getConnection();
                            $connection->delete(self::TABLE_NAME,
                                                [
                                                    'entity_id IN (?)'    => $productId,
                                                    'value IN (?)'        => $ruleId,
                                                    'attribute_id IN (?)' => $attribute->getAttributeId()
                                                ]
                            );
                }catch (\Exception $e)
                {
                    $this->logger->error($e->getMessage());
                }
                break;
            case 2 :
                try {
                    $data  = [
                        "attribute_id" => $attribute->getAttributeId(),
                        "store_id"     => 0,
                        "entity_id"    => $productId,
                        "value"        => $ruleId
                    ];

                    $connection = $this->resource->getConnection();
                    $connection->insertOnDuplicate(self::TABLE_NAME, $data);
                } catch (\Exception $e) {
                    $this->logger->error($e->getMessage());
                }
                break;
            case 3:
                break;
            }

            return $this;
    }
}
