<?php
namespace Ecommage\CustomCmsPage\Ui\Component\Form;

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
            ['value' => 1, 'label' => __('Yes')],
            ['value' => 0, 'label' => __('No')]
        ];
    }
}
