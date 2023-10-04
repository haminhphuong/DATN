<?php

namespace Ecommage\CustomOptionsImage\Block\Product\View\Options\Type\Select;

use Magento\Catalog\Api\Data\ProductCustomOptionValuesInterface;

class Checkable extends \Magento\Catalog\Block\Product\View\Options\Type\Select\Checkable
{
    protected $_template = 'Ecommage_CustomOptionsImage::product/composite/fieldset/options/view/checkable.phtml';

    protected $storeManager;

    protected $currencyFactory;

    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Framework\Pricing\Helper\Data           $pricingHelper,
        \Magento\Catalog\Helper\Data                     $catalogData,
        \Magento\Store\Model\StoreManagerInterface       $storeManager,
        \Magento\Directory\Model\CurrencyFactory $currencyFactory,
        array                                            $data = []
    ) {
        parent::__construct($context, $pricingHelper, $catalogData, $data);
        $this->storeManager = $storeManager;
        $this->currencyFactory = $currencyFactory;
    }

    public function getImageUrl($file)
    {
        $mediaUrl = $this->_storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA);
        if ($file) {
            return $mediaUrl . $file;
        }
        return '';
    }

    public function getCurrencySymbol()
    {
        $currencyCode = $this->storeManager->getStore()->getCurrentCurrencyCode();
        return $this->currencyFactory->create()->load($currencyCode)->getCurrencySymbol();
    }
}
