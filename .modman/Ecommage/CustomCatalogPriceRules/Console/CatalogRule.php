<?php

namespace Ecommage\CustomCatalogPriceRules\Console;

use Ecommage\CustomCatalogPriceRules\Helper\Data;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class CatalogRule extends Command
{
    /**
     * @var Data
     */
    protected $helperData;

    /**
     * CatalogRule constructor.
     *
     * @param Data        $helperData
     * @param string|null $name
     */
    public function __construct(
        Data $helperData,
        string $name = null
    ) {
        $this->helperData = $helperData;
        parent::__construct($name);
    }

    /**
     * @return void
     */
    protected function configure()
    {
        $this->setName('ecommage:update:catalog-rule');
        $this->setDescription('This is my console command run in update catalog rule!');
        parent::configure();
    }

    /**
     * @param InputInterface  $input
     * @param OutputInterface $output
     * @SuppressWarnings(PHPMD.ElseExpression)
     *
     * @return void
     * @throws LocalizedException
     * @throws NoSuchEntityException
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $productIds = $this->helperData->getAllProductId();
        $this->helperData->indexRows($productIds);
    }

}
