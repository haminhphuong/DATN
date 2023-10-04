<?php

namespace Ecommage\AutoGenerateMetaContent\Command;

use Magento\Framework\Console\Cli;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Magento\Catalog\Model\ResourceModel\Product\Collection;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory;
use Magento\Catalog\Api\Data\ProductAttributeInterface as Product;
use Magento\Catalog\Model\Product\Action as ProductAction;
use Ecommage\AutoGenerateMetaContent\Helper\Html2Text;

/**
 * Class UpdateMetaData
 *
 * @package Ecommage\AutoGenerateMetaContent\Command
 */
class UpdateMetaData extends Command
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
     * @inheritdoc
     */
    protected function configure()
    {
        $this->setName("catalog:product:meta-data:update");
        $this->setDescription("Automatically update meta data for product.");
        parent::configure();
    }

    /**
     * UpdateMetaData constructor.
     *
     * @param CollectionFactory $collectionFactory
     * @param ProductAction     $productAction
     * @param string|null       $name
     */
    public function __construct(
        CollectionFactory $collectionFactory,
        ProductAction $productAction,
        string $name = null
    ) {
        $this->productAction     = $productAction;
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
                'meta_title',
                'description',
                'short_description',
                'meta_description'
            ]
        );
//        $collection->addAttributeToFilter(
//            [
//                ['attribute' => 'meta_title' , 'eq' => ''],
//                ['attribute' => 'meta_description' , 'eq' => '']
//            ]
//        );
        $step = 0;
        $totalProducts = $collection->getSize();
        foreach ($collection as $product) {
            $step++;
            $metaData = [];
            if (!$product->getData('meta_title')) {
                $metaTitle = $this->getMetaData($product->getName());
                $metaData[Product::CODE_SEO_FIELD_META_TITLE] = $metaTitle;
            }

            if (!$product->getData('meta_description')) {
                $content = $product->getData('short_description');
                if (empty($metaData)) {
                    $content = $product->getData('description');
                }

                $string = $this->getMetaData($content);
                if (!empty($string)) {
                    $metaData[Product::CODE_SEO_FIELD_META_DESCRIPTION] = $string;
                }
            }

            if (!empty($metaData)) {
                $this->productAction->updateAttributes(
                    [$product->getEntityId()],
                    $metaData,
                    0
                );
            }

            $output->writeln(sprintf('Products processed: %s/%s', $step, $totalProducts));
        }

        return Cli::RETURN_SUCCESS;
    }

    /**
     * @param     $html
     * @param int $limit
     *
     * @return string
     */
    protected function getMetaData($html, $limit = 255)
    {
        $html = new Html2Text($html);
        return $html->getShortText($limit);
    }
}
