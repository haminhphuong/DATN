<?php

namespace Ecommage\BannerManager\Model\System\Config;

use Magento\Framework\Option\ArrayInterface;

class Transition implements ArrayInterface
{
    /**
     * @var array
     */
    protected $options = [];

    /**
     * @return array
     */
    public function toOptionArray()
    {
        if (empty($this->options)) {
            foreach ($this->toArray() as $value => $label) {
                $this->options[] = [
                    'value' => $value,
                    'label' => $label
                ];
            }
        }

        return $this->options;
    }

    /**
     * @return array
     */
    public function toArray()
    {
        return [
            ''      => __('None'),
            'left'  => __('Left'),
            'right' => __('Right'),
            'down'  => __('Down'),
            'up'    => __('Up')
        ];
    }
}
