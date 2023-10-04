<?php
namespace Ecommage\CustomCatalog\Plugin\Catalog\Block;

class Toolbar
{
    protected $arrSortAttr = ['low_to_high','high_to_low','name_az','name_za'];

    /**
     * @param \Magento\Catalog\Block\Product\ProductList\Toolbar $subject
     * @param \Closure                                           $proceed
     * @param                                                    $collection
     *
     * @return mixed
     * @SuppressWarnings(PHPMD.ElseExpression)
     */
    public function aroundSetCollection(
        \Magento\Catalog\Block\Product\ProductList\Toolbar $subject,
        \Closure $proceed,
                                                           $collection
    ) {
        $currentOrder = $subject->getCurrentOrder();
        $result = $proceed($collection);
        if ($currentOrder) {
            if ($currentOrder == 'high_to_low') {
                $subject->getCollection()->setOrder('price', 'desc');
            }

            if ($currentOrder == 'low_to_high') {
                $subject->getCollection()->setOrder('price', 'asc');

            }

            if($currentOrder == 'name_az') {
                $subject->getCollection()->setOrder('name', 'asc');

            }

            if($currentOrder == 'name_za') {
                $subject->getCollection()->setOrder('name', 'desc');
            }



            if(!in_array($currentOrder, $this->arrSortAttr)) {
                $subject->getCollection()->setOrder($currentOrder, $subject->getCurrentDirection());
            }

        }

        return $result;
    }

}
