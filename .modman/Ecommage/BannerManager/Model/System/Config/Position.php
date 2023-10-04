<?php

namespace Ecommage\BannerManager\Model\System\Config;

use Magento\Framework\Option\ArrayInterface;

class Position implements ArrayInterface
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
            ''             => __('None'),
            'bottomLeft'   => __('Bottom Left'),
            'bottomRight'  => __('Bottom Right'),
            'centerCenter' => __('Center Center'),
            'topRight'     => __('Top Right'),
            'topLeft'      => __('Top Left'),
        ];
    }
}
