<?php
namespace Ecommage\Backend\Model\Config\Source;
use Magento\Eav\Model\Entity\Attribute\Source\AbstractSource;

class Status extends AbstractSource
{
    /**
     * @return array[]|null
     */
    public function getAllOptions(): ?array
    {
        if (!$this->_options) {
            $this->_options = [
                ['label' => __('Disable'), 'value' => 0],
                ['label' => __('Enable'), 'value' => 1]
            ];
        }
        return $this->_options;
    }
}
