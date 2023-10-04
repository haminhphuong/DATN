<?php

namespace Ecommage\BannerManager\Model\System\Config;

use Magento\Framework\Option\ArrayInterface;

class ClassList implements ArrayInterface
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
            'sp-layer'           => 'sp-layer',
            'sp-white'           => 'sp-white',
            'sp-padding'         => 'sp-padding',
            'sp-black'           => 'sp-black',
            'sp-static'          => 'sp-static',
            'hide-small-screen'  => 'hide-small-screen',
            'hide-medium-screen' => 'hide-medium-screen',
        ];
    }
}
