<?php

namespace Ecommage\CustomCatalogPriceRules\Observer;

use Exception;
use Magento\Catalog\Model\Product;
use Magento\Eav\Api\AttributeRepositoryInterface;
use Magento\Framework\App\ResourceConnection;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Stdlib\DateTime\DateTime;
use Magento\SalesRule\Model\Rule;
use Magento\Store\Model\StoreManagerInterface;
use Psr\Log\LoggerInterface;

class UpdateAttributeCatalogRule implements ObserverInterface
{
    const TABLE_NAME = 'catalog_product_entity_int';

    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * @var ResourceConnection
     */
    protected $resource;

    /**
     * @var DateTime
     */
    protected $date;

    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var AttributeRepositoryInterface
     */
    private $attributeRepository;


    public function __construct(
        StoreManagerInterface        $storeManager,
        DateTime                     $date,
        LoggerInterface              $logger,
        AttributeRepositoryInterface $attributeRepository,
        ResourceConnection           $resourceConnection
    )
    {
        $this->attributeRepository = $attributeRepository;
        $this->resource = $resourceConnection;
        $this->logger = $logger;
        $this->date = $date;
        $this->storeManager = $storeManager;
    }

    /**
     * @param Observer $observer
     *
     * @return void
     */
    public function execute(Observer $observer)
    {
        /** @var \Magento\CatalogRule\Model\Rule $rule */
        $rule = $observer->getEvent()->getRule();
        if (!empty($rule->getStatus())){
            $this->setIsActiveRules($rule->getId(),1);
        }
        $fromDate = $rule->getFromDate();
        $toDate = $rule->getToDate();
        $productIds = $rule->getMatchingProductIds();
        $websiteIds = $rule->getWebsiteIds();
        $this->clearProductRule($this->getProduct($productIds), $rule->getId());
        $status = empty($rule->getStatus()) ? $rule->getIsActive() : $rule->getStatus();
        foreach ($websiteIds as $websiteId) {
            $storeId = $this->getStoreId($websiteId);
            if ($this->setCompareDay(strtotime($fromDate), strtotime($toDate))
                && !empty($status)
                && $rule->getSimpleAction() == Rule::TO_FIXED_ACTION) {
                $this->updateProductRule($this->getProduct($productIds), $rule->getId());
            }
        }
    }

    /**
     * @param $productIds
     * @param $ruleId
     *
     * @return $this
     * @throws NoSuchEntityException
     */
    protected function clearProductRule($productIds, $ruleId)
    {

        if (!empty($productIds)) {
            try {
                $attribute = $this->attributeRepository->get(Product::ENTITY, 'catalog_rule');
                foreach ($productIds as $items) {
                    $connection = $this->resource->getConnection();
                    $connection->delete(self::TABLE_NAME,
                        [
                            'entity_id IN (?)' => $items,
                            'value IN (?)' => $ruleId,
                            'attribute_id IN (?)' => $attribute->getAttributeId()
                        ]
                    );
                }
            } catch (Exception $e) {
                $this->logger->error($e->getMessage());
            }

        }

        return $this;
    }

    /**
     * @param $collocation
     *
     * @return array
     */
    protected function getProduct($collocation)
    {
        $arr = [];
        foreach ($collocation as $key => $item) {
            $arr[] = $key;
        }
        return $arr;
    }

    /**
     * @param $websiteId
     *
     * @return int
     * @throws LocalizedException
     */
    public function getStoreId($websiteId)
    {
        return $this->storeManager->getWebsite($websiteId)->getDefaultStore()->getId();
    }

    /**
     * @param $fromDate
     * @param $toDate
     *
     * @return bool
     */
    public function setCompareDay($fromDate, $toDate)
    {
        $currentDate = strtotime($this->date->gmtDate('Y-m-d'));
        if (($fromDate && $currentDate < $fromDate) || ($toDate && $currentDate > $toDate)) {
            return false;
        }
        return true;
    }

    /**
     * @param $productIds
     * @param $attributeUpdate
     * @param int $storeId
     * @return $this
     * @throws NoSuchEntityException
     */
    protected function updateProductRule($productIds, $attributeUpdate)
    {
        $attribute = $this->attributeRepository->get(Product::ENTITY, 'catalog_rule');
        foreach ($productIds as $productId) {
            try {
                $data = [
                    "attribute_id" => $attribute->getAttributeId(),
                    "store_id" => 0,
                    "entity_id" => $productId,
                    "value" => $attributeUpdate
                ];

                $connection = $this->resource->getConnection();
                $connection->insertOnDuplicate(self::TABLE_NAME, $data);
            } catch (Exception $e) {
                $this->logger->error($e->getMessage());
            }
        }

        return $this;
    }

    /**
     * @param $ruleId
     * @param $value
     *
     * @return void
     */
    public function setIsActiveRules($ruleId, $value){
        if ($ruleId){
            try {
                $connection = $this->resource->getConnection();
                $tableName = "catalogrule";
                $connection->update($tableName,
                                    [
                                        'is_active' => $value
                                    ],
                                    [
                                        'rule_id IN (?)'    => $ruleId,
                                    ]
                );
            }catch (\Exception $e) {
                $this->_objectManager->get(\Psr\Log\LoggerInterface::class)->error($e->getMessage());
            }
        }
        return $this;
    }

}
