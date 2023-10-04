<?php

namespace Ecommage\CustomCatalog\Cron;

use Magento\Catalog\Model\Product\Action as ProductAction;
use Magento\Catalog\Model\ResourceModel\Product\Collection;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory;
use Magento\CatalogInventory\Api\StockRegistryInterface;
use Magento\CatalogInventory\Api\Data\StockItemInterface;

class CronUpdateStockStatus
{

    /**
     * @var \Magento\Store\Model\App\Emulation
     */
    private $emulation;

    /**
     * @var
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
     * CronUpdateCatalogRule constructor.
     * @param ProductAction $productAction
     * @param CollectionFactory $collectionFactory
     * @param StockRegistryInterface $stockRegistry
     * @param \Magento\Store\Model\App\Emulation $emulation
     */
    public function __construct
    (
        ProductAction $productAction,
        CollectionFactory $collectionFactory,
        StockRegistryInterface $stockRegistry,
        \Magento\Store\Model\App\Emulation $emulation
    ) {
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
        $this->emulation->startEnvironmentEmulation(1, \Magento\Framework\App\Area::AREA_FRONTEND, true); // You can set store id and area

        /** @var Collection $collection */
        $collection = $this->collectionFactory->create();
        $collection->addAttributeToSelect(
            [
                'name',
                'quantity_and_stock_status'
            ]
        );
        $step = 0;
        foreach ($collection as $product) {
            $step++;
            $stockStatus = [];
            $stockStatus['stock_status'] = 0;
            if (!$this->getStockStatus($product->getEntityId())) {
                $stockStatus['stock_status'] = 1;
            }
            $this->productAction->updateAttributes(
                [$product->getEntityId()],
                $stockStatus,
                0
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
