<?php
namespace Ecommage\WidgetCatalogPriceRule\Model\Config;

use Ecommage\WidgetCatalogPriceRule\Helper\CategoryData;
use Magento\Framework\Data\OptionSourceInterface;

class WidgetListCatalog implements OptionSourceInterface
{
    protected $locationCollection;
    protected $helperData;

    /**
     * WidgetListCatalog constructor.
     * @param CategoryData $helperData
     */
    public function __construct(
        CategoryData $helperData
    )
    {

        $this->helperData = $helperData;
    }

    /**
     * @return array
     */
    public function toOptionArray()
    {

        $lists = [];
        $provinces = $this->helperData->getCategoryCollection();
        foreach ($provinces as $province) {
            $lists[] = [
                'value' => $province->getId(),
                'label' => $province->getName()
            ];
        }
        return $lists;
    }


}
