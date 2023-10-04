<?php

namespace Ecommage\PageBuilder\Plugin;
use Magento\Framework\HTTP\Header;
use Magento\CatalogWidget\Block\Product\ProductsList;
class ProductListMobile
{
    /**
     * @var Header
     */
    protected $httpHeader;
    public function __construct(Header $httpHeader)
    {
        $this->httpHeader = $httpHeader;
    }

    /**
     * @param ProductsList $productsList
     * @param callable $proceed
     * @return array|mixed|null
     */
    public function aroundGetProductsCount(ProductsList $productsList, callable $proceed)
    {
        $userAgent = $this->httpHeader->getHttpUserAgent();
        $isMobile = \Zend_Http_UserAgent_Mobile::match($userAgent, $_SERVER);
        if($isMobile){
            if ($productsList->hasData('products_count_mobile')) {
                return $productsList->getData('products_count_mobile');
            }
        }
        if ($productsList->hasData('products_count')) {
            return $productsList->getData('products_count');
        }

        if (null === $productsList->getData('products_count')) {
            $productsList->setData('products_count', $productsList::DEFAULT_PRODUCTS_COUNT);
        }

        return $productsList->getData('products_count');
    }
}
