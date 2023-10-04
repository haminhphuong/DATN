<?php

namespace Ecommage\ProductSaleRule\Block;

use Magento\Backend\Block\Template\Context;
use Magento\Catalog\Model\Product;
use Magento\CatalogRule\Model\RuleFactory;
use Magento\CatalogRule\Model\ResourceModel\Rule;
use Magento\Customer\Model\Session;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Registry;
use Magento\Framework\View\Element\Template;
use Magento\Store\Model\StoreManagerInterface;

class RuleData extends Template
{
    /**
     * @var Registry
     */
    protected $_registry;
    /**
     * @var RuleFactory
     */
    protected $ruleFactory;
    /**
     * @var Session
     */
    protected $customerSession;
    /**
     * @var StoreManagerInterface
     */
    protected $_storeManager;
    /**
     * @var Rule
     */
    protected $rule;

    /**
     * @param Context $context
     * @param Registry $registry
     * @param RuleFactory $ruleFactory
     * @param Session $customerSession
     * @param StoreManagerInterface $storeManager
     * @param Rule $rule
     * @param array $data
     */
    public function __construct(
        Context               $context,
        Registry              $registry,
        RuleFactory           $ruleFactory,
        Session               $customerSession,
        StoreManagerInterface $storeManager,
        Rule                  $rule,
        array                 $data = []
    )
    {
        $this->_registry = $registry;
        $this->ruleFactory = $ruleFactory;
        $this->customerSession = $customerSession;
        $this->_storeManager = $storeManager;
        $this->rule = $rule;
        parent::__construct($context, $data);
    }

    /**
     * @return RuleData
     */
    public function _prepareLayout()
    {
        return parent::_prepareLayout();
    }

    /**
     * @return array
     * @throws LocalizedException
     * @throws NoSuchEntityException
     */
    public function getRulesProduct()
    {
        $productRuleSale = [];
        $websiteId = (int)$this->_storeManager->getStore((int)$this->getCurrentProduct()->getStoreId())->getWebsiteId();
        $customerGroupId = 0;
        $product = $this->getCurrentProduct();
        if ($this->customerSession->isLoggedIn()) {
            $customerGroupId = $this->customerSession->getCustomerGroupId();
        }

        // check product type && handle get rule data
        switch ($product->getTypeId()) {
            case 'configurable':
                $productVariants = $product->getTypeInstance()->getUsedProducts($product);
                $productId = $productVariants[0]->getId();
                $ruleData = $this->rule->getRulesFromProduct(date('m/d/Y'), $websiteId, $customerGroupId, $productId);
                break;
            case 'bundle':
                return $productRuleSale;
            default:
                //simple
                $ruleData = $this->rule->getRulesFromProduct(date('m/d/Y'), $websiteId, $customerGroupId, (int)$product->getId());
                break;
        }

        if (!empty($ruleData)) {
            foreach ($ruleData as $key => $rule) {
                /** @var  $catalogRule */

                $catalogRule = $this->getCatalogRuleModel($rule['rule_id']);
                // promotion
                if ($catalogRule->getData('rule_type') == 1) {
                    $productRuleSale['promotion'][$key] = $catalogRule->getData();
                } else {
                    $productRuleSale['special_offer'][$key] = $catalogRule->getData();
                }
            }
        }
        return $productRuleSale;
    }

    /**
     * @return Product|null
     */
    public function getCurrentProduct()
    {
        return $this->_registry->registry('current_product');
    }

    /**
     * @param $ruleId
     * @return \Magento\CatalogRule\Model\Rule
     */
    public function getCatalogRuleModel($ruleId)
    {
        return $this->ruleFactory->create()->load($ruleId);
    }
}
