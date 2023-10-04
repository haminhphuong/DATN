<?php

namespace Ecommage\CustomAmastyPromo\Plugin\Helper;

use Amasty\Promo\Helper\Messages;
use Amasty\Promo\Model\ItemRegistry\PromoItemData;
use Amasty\Promo\Model\Product as ProductStock;
use Magento\Catalog\Model\Product;
use Magento\Framework\Exception\LocalizedException;
use Magento\Quote\Model\Quote;

/**
 * Add promo items to cart
 */
class Cart
{
    /**
     * @var Messages
     */
    protected $promoMessagesHelper;

    /**
     * @var ProductStock
     */
    protected $product;

    /**
     * @param Messages $promoMessagesHelper
     * @param ProductStock $product
     */
    public function __construct(
        Messages $promoMessagesHelper,
        ProductStock $product
    ) {
        $this->promoMessagesHelper = $promoMessagesHelper;
        $this->product = $product;
    }

    /**
     * @param Product $product
     * @param int $qty
     * @param PromoItemData $promoItemData
     * @param array $requestParams
     * @param Quote|null $quote
     *
     * @return bool
     */
    public function aroundAddProduct(
        \Amasty\Promo\Helper\Cart $subject,
        callable $proceed,
        Product $product,
                $qty,
                $promoItemData,
        array $requestParams,
        Quote $quote
    ) {
        if ($product->getTypeId() == \Magento\Catalog\Model\Product\Type::TYPE_SIMPLE) {
            $qty = $this->resolveQty($product, $qty, $quote);
        }
        if ($qty == 0) {
            return false;
        }

        $ruleId = $promoItemData->getRuleId();
        //TODO ST-1949 process not free items with custom_price
        $requestParams['qty'] = $qty;
        $requestParams['options']['ampromo_rule_id'] = $ruleId;
        $requestParams['options']['discount'] = $promoItemData->getDiscountArray();

        if ($product->getTypeId() == \Magento\Catalog\Model\Product\Type::TYPE_BUNDLE) {
            if (!isset($requestParams['bundle_option'])) {
                $requestParams = array_merge($requestParams, $this->getBundleOptions($product));
            }
        }

        try {
            $item = $quote->addProduct($product, new \Magento\Framework\DataObject($requestParams));

            if ($item instanceof \Magento\Quote\Model\Quote\Item) {
                $item->setData('ampromo_rule_id', $ruleId);
            } else {
                throw new LocalizedException(__($item));
            }

            //qty for promoItemData will be reserved later
            $promoItemData->isDeleted(false);
            return true;
        } catch (\Exception $e) {
            $this->promoMessagesHelper->showMessage(
                $e->getMessage(),
                true,
                true
            );
        }

        return false;
    }

    /**
     * Get all the default selection products used in bundle product
     * @param Product $product
     * @return array
     */
    private function getBundleOptions(Product $product)
    {
        $selectionCollection = $product->getTypeInstance()
            ->getSelectionsCollection(
                $product->getTypeInstance()->getOptionsIds($product),
                $product
            );
        $bundleOptions = [];
        foreach ($selectionCollection as $selection) {
            if (!$selection->getIsDefault()) {
                continue;
            }

            $bundleOptions['bundle_option'][$selection->getOptionId()][] = $selection->getSelectionId();
            $bundleOptions['bundle_option_qty'][$selection->getOptionId()] = $selection->getSelectionQty();
        }

        return $bundleOptions;
    }

    /**
     * @param Product $product
     * @param int $qty
     * @param Quote $quote
     *
     * @return float|int
     */
    private function resolveQty($product, $qty, $quote)
    {
        $availableQty = $this->product->checkAvailableQty($product->getSku(), $qty, $quote);

        if ($availableQty <= 0) {
            $this->promoMessagesHelper->addAvailabilityError($product);

            $availableQty = 0;
        } elseif ($availableQty < $qty) {
            $this->promoMessagesHelper->showMessage(
                __(
                    "We apologize, but requested quantity of free gift <strong>%1</strong>"
                    . " is not available at the moment",
                    $product->getName()
                ),
                false,
                true
            );
        }

        return $availableQty;
    }
}
