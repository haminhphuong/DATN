<?php
declare(strict_types=1);

namespace Ecommage\CustomCatalogPriceRules\Model\Config;


class Options extends \Magento\Eav\Model\Entity\Attribute\Source\AbstractSource
{

    public function getAllOptions()
    {
             return [
                 ['label' => '', 'value' => ''],
                ['label' => __('0% - 10%'), 'value' => '0-10'],
                ['label' => __('10% - 20%'), 'value' => '10-20'],
                ['label' => __('20% - 30%'), 'value' => '20-30'],
                ['label' => __('30% - 40%'), 'value' => '30-40'],
                ['label' => __('40% - 50%'), 'value' => '40-50'],
                ['label' => __('Over 50%'), 'value' => '51-100']
            ];
    }
}
