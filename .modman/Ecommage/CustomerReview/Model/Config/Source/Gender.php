<?php

namespace Ecommage\CustomerReview\Model\Config\Source;

use Magento\Eav\Model\Entity\Attribute\Source\AbstractSource;

class Gender extends AbstractSource
{
    public function getAllOptions(): ?array
    {
        if (!$this->_options) {
            $this->_options = [
                ['label' => __('Male'), 'value' => 0],
                ['label' => __('Female'), 'value' => 1],
            ];
        }
        return $this->_options;
    }
}
