<?php
namespace Ecommage\TabsPro\Plugin\Block\Widget;

use Ecommage\TabsPro\Helper\Data;

class Tab
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

    /**
     * Get key is mobile for caching block content
     *
     * @return array
     */
    public function afterGetCacheKeyInfo(\Magezon\TabsPro\Block\Widget\Tab $subject, $result)
    {
        $result[] = $this->helper->isMobile();
        return $result;
    }
}
