<?php

namespace Ecommage\CustomerReview\Model\Config\Source;

use Magento\Eav\Model\Entity\Attribute\Source\AbstractSource;

class Type extends AbstractSource
{
    public function getAllOptions(): ?array
    {
        if (!$this->_options) {
            $this->_options = [
                ['label' => __('Video'), 'value' => 'video'],
                ['label' => __('Image'), 'value' => 'image'],
            ];
        }
        return $this->_options;
    }
}
