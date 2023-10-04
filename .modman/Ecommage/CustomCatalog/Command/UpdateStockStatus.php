<?php

namespace Ecommage\CustomCatalog\Command;

use Magento\Catalog\Model\Product\Action as ProductAction;
use Magento\Framework\Console\Cli;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Magento\Catalog\Model\ResourceModel\Product\Collection;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory;
use Magento\Catalog\Api\Data\ProductAttributeInterface as Product;
use Ecommage\CustomCatalog\Helper\Data;
use Magento\CatalogInventory\Api\StockRegistryInterface;
use Magento\CatalogInventory\Api\Data\StockItemInterface;

class UpdateStockStatus extends Command
{
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
     * @inheritdoc
     */
    protected function configure()
    {
        $this->setName("catalog:product:stock-status:update");
        $this->setDescription("Automatically update stock status for product.");
        parent::configure();
    }

    /**
     * UpdateStockStatus constructor.
     * @param CollectionFactory $collectionFactory
     * @param ProductAction $productAction
     * @param StockRegistryInterface $stockRegistry
     * @param string|null $name
     */
    public function __construct(
        CollectionFactory $collectionFactory,
        ProductAction $productAction,
        StockRegistryInterface $stockRegistry,
        string $name = null
    ) {
        $this->productAction     = $productAction;
        $this->stockRegistry = $stockRegistry;
        $this->collectionFactory = $collectionFactory;
        parent::__construct($name);
    }

    /**
     * @param InputInterface  $input
     * @param OutputInterface $output
     *
     * @return int|void
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        /** @var Collection $collection */
        $collection = $this->collectionFactory->create();
        $collection->addAttributeToSelect(
            [
                'name',
                'quantity_and_stock_status'
            ]
        );
        $step = 0;
        $totalProducts = $collection->getSize();
        foreach ($collection as $product) {
            $step++;
            $stockStatus = [];
            if (!$this->getStockStatus($product->getEntityId())) {
                $stockStatus['stock_status'] = 1;
            } else {
                $stockStatus['stock_status'] = 0;
            }

            $this->productAction->updateAttributes(
                [$product->getEntityId()],
                $stockStatus,
                0
            );

            $output->writeln(sprintf('Products processed: %s/%s', $step, $totalProducts));
        }

        return Cli::RETURN_SUCCESS;
    }

    /**
     * @param      $html
     * @param int  $limit
     * @param null $endSubstitute
     * @param bool $lineBreak
     *
     * @return string
     */
    protected function getMetaData($html, $limit = 255, $endSubstitute = null, $lineBreak = true)
    {
        return Data::getContent($html, $limit, $endSubstitute, $lineBreak);
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
