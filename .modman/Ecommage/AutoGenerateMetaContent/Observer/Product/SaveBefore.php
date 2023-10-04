<?php

namespace Ecommage\AutoGenerateMetaContent\Observer\Product;

use Ecommage\AutoGenerateMetaContent\Helper\Html2Text;

/**
 * Class SaveBefore
 */
class SaveBefore implements \Magento\Framework\Event\ObserverInterface
{
    /**
     * @var \Magento\Cms\Model\Template\FilterProvider
     */
    protected $_filterProvider;

    /**
     * SaveBefore constructor.
     *
     * @param \Magento\Cms\Model\Template\FilterProvider $filterProvider
     */
    public function __construct(\Magento\Cms\Model\Template\FilterProvider $filterProvider)
    {
        $this->_filterProvider = $filterProvider;
    }

    /**
     * @param \Magento\Framework\Event\Observer $observer
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $product = $observer->getEvent()->getData('product');
        if (!$product->getData('meta_title')) {
            $metaTitle = $this->getMetaData($product->getName());
            $product->setData('meta_title', $metaTitle);
        }

        $metaDescription = $product->getData('meta_description');
        if (!$metaDescription || $this->isHtml($metaDescription)) {
            $metaData = $product->getData('short_description');
            if (empty($metaData)) {
                $metaData = $product->getData('description');
                $metaData = $this->_filterProvider->getBlockFilter()->filter($metaData);
            }

            $metaContent = $this->getMetaData($metaData);
            $product->setData('meta_description', $metaContent);
        }
    }

    /**
     * @param $string
     *
     * @return bool
     */
    private function isHtml($string)
    {
        if (
            $string != strip_tags($string) ||
            strpos($string, '#html-body') !== false
        ) {
            return true;
        }

        return false;
    }

    /**
     * @param     $html
     * @param int $limit
     *
     * @return string
     */
    protected function getMetaData($html, $limit = 255)
    {
        $html = new Html2Text($html);
        return $html->getShortText($limit);
    }
}
