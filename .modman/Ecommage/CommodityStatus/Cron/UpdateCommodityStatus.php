<?php

namespace Ecommage\CommodityStatus\Cron;

use Magento\Catalog\Model\Product\Action as ProductAction;
use Magento\Catalog\Model\ResourceModel\Product\Collection;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory;
use Ecommage\CommodityStatus\Model\Config\Source\Product\Status;
use Magento\CatalogInventory\Api\StockRegistryInterface;
use Magento\CatalogInventory\Api\Data\StockItemInterface;
use Magento\Store\Model\StoreManagerInterface;

/**
 * Class UpdateCommodityStatus
 *
 * @package Ecommage\CustomCatalog\Cron
 */
class UpdateCommodityStatus
{
    /**
     * @var \Magento\Store\Model\App\Emulation
     */
    private $emulation;
    /**
     * @var ProductAction
     */
    protected $productAction;
    /**
     * @var CollectionFactory
     */
    protected $collectionFactory;

    /**
     * @var StockRegistryInterface|null
     */
    private $stockRegistry;

    /**
     * @var StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @param ProductAction $productAction
     * @param CollectionFactory $collectionFactory
     * @param StockRegistryInterface $stockRegistry
     * @param \Magento\Store\Model\App\Emulation $emulation
     * @param StoreManagerInterface $storeManager
     */
    public function __construct
    (
        ProductAction $productAction,
        CollectionFactory $collectionFactory,
        StockRegistryInterface $stockRegistry,
        \Magento\Store\Model\App\Emulation $emulation,
        StoreManagerInterface $storeManager
    ) {
        $this->storeManager = $storeManager;
        $this->emulation = $emulation;
        $this->productAction     = $productAction;
        $this->stockRegistry = $stockRegistry;
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
        $storeId = $this->storeManager->getStore()->getId();
        $this->emulation->startEnvironmentEmulation($storeId, \Magento\Framework\App\Area::AREA_FRONTEND, true); // You can set store id and area
        /** @var Collection $collection */
        $collection = $this->collectionFactory->create();
        $collection->addAttributeToSelect(
            [
                'name',
                'quantity_and_stock_status',
                'commodity_status'
            ]
        );
        foreach ($collection as $product) {
            // ignore commodity_status == COMMING SOON
            if($product->getCommodityStatus() && $product->getCommodityStatus() == Status::STATUS_COMING_SOON){
                continue;
            }
            $stockStatus = [];
            $stockStatus[Status::ATTRIBUTE_CODE] = Status::STATUS_OUT_OF_STOCK;

            if ($this->getStockStatus($product->getId())) {
                $stockStatus[Status::ATTRIBUTE_CODE] = Status::STATUS_IN_STOCK;
            }

            $this->productAction->updateAttributes(
                [$product->getEntityId()],
                $stockStatus,
                $storeId
            );
        }

        $this->emulation->stopEnvironmentEmulation();
        shell_exec('php bin/magento indexer:reset');
        shell_exec('php bin/magento indexer:reindex');
    }


    /**
     * get stock status
     *
     * @param int $productId
     * @return bool
     */
    public function getStockStatus($productId)
    {
        /** @var StockItemInterface $stockItem */
        $stockItem = $this->stockRegistry->getStockItem($productId);
        return $stockItem ? $stockItem->getIsInStock() : false;
    }
}
