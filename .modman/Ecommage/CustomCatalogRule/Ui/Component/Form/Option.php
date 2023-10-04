<?php
namespace Ecommage\CustomCatalogRule\Ui\Component\Form;

/**
 * Class Status
 * @package
 */
class Option implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * @return array[]
     */
    public function toOptionArray()
    {
        return [
            ['value' => 0, 'label' => __('Special offer')],
            ['value' => 1, 'label' => __('Promotion')]
        ];
    }
}
