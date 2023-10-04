<?php

namespace Ecommage\CustomCatalogPriceRules\Model\Config\Source;

use Magento\CatalogRule\Model\ResourceModel\Rule\CollectionFactory;
use Magento\CatalogRule\Model\Rule;
use Magento\Eav\Model\Entity\Attribute\Source\AbstractSource;

class Rules extends AbstractSource
{
    /**
     * @var null
     */
    protected $_options = null;
    /**
     * @var CollectionFactory
     */
    protected $collectionFactory;

    /**
     * @param CollectionFactory $collectionFactory
     */
    public function __construct(CollectionFactory $collectionFactory)
    {
        $this->collectionFactory = $collectionFactory;
    }

    /**
     * @return null
     */
    public function getAllOptions()
    {
        if (!$this->_options) {
            $this->_options[] = [
                'value' => '',
                'label' => __('Please select catalog rule')
            ];
            $collection = $this->collectionFactory->create()
                ->addFieldToFilter('simple_action', 'to_fixed');
            $collection->setOrder('rule_id', 'DESC');
            /** @var Rule $rule */
            foreach ($collection as $rule) {
                $this->_options[] = [
                    'value' => $rule->getId(),
                    'label' => empty($rule->getDisplayFilter())? $rule->getName() : $rule->getDisplayFilter()
                ];
            }
        }

        return $this->_options;
    }
}
