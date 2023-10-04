<?php
namespace Ecommage\CustomCatalog\Model\Config\Source;

use Magento\Eav\Model\Entity\Attribute\Source\AbstractSource;

class StockStatus extends AbstractSource
{
    protected $_options = null;
    /**
     * Get all options
     *
     * @return array
     */
    public function getAllOptions()
    {
        if (null === $this->_options) {
            $this->_options=[
                ['label' => __('In Stock'), 'value' => 0],
                ['label' => __('Out Stock'), 'value' => 1],
                ['label' => __('Goods are coming'), 'value' => 2]
            ];
        }
        return $this->_options;
    }
}