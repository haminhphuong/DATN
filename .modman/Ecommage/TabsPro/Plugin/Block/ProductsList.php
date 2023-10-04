<?php
namespace Ecommage\TabsPro\Plugin\Block;

use Ecommage\TabsPro\Helper\Data;

/**
 * Class ProductsList
 */
class ProductsList
{
    /**
     * @var Data
     */
    private $helper;

    /**
     * @param Data $helper
     */
    public function __construct
    (
        Data $helper
    )
    {
        $this->helper = $helper;
    }
    //function beforeMETHOD($subject, $arg1, $arg2){}
    //function aroundMETHOD($subject, $proceed, $arg1, $arg2){return $proceed($arg1, $arg2);}
    /**
     * @param \Magezon\TabsPro\Block\ProductsList $subject
     * @param            $result
     *
     * @return string
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function afterGetTemplate(\Magezon\TabsPro\Block\ProductsList $subject, $result)
    {
        return 'Ecommage_TabsPro::productlist.phtml';
    }

    /**
     * Get key is mobile for caching block content
     *
     * @return array
     */
    public function afterGetCacheKeyInfo(\Magezon\TabsPro\Block\ProductsList $subject, $result)
    {
        $result[] = $this->helper->isMobile();
        return $result;
    }
}
