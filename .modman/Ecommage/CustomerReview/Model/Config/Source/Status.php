<?php

namespace Ecommage\CustomerReview\Model\Config\Source;

use Magento\Eav\Model\Entity\Attribute\Source\AbstractSource;

class Status extends AbstractSource
{
    public function getAllOptions(): ?array
    {
        if (!$this->_options) {
            $this->_options = [
                ['label' => __('Inactive'), 'value' => 0],
                ['label' => __('Active'), 'value' => 1],
            ];
        }
        return $this->_options;
    }
}
