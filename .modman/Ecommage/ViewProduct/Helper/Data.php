<?php

namespace Ecommage\ViewProduct\Helper;

use Amasty\Label\Helper\Config;
use Amasty\Label\Model\AbstractLabels;
use Amasty\Label\Model\Labels;
use Amasty\Label\Model\LabelsFactory;
use Amasty\Label\Model\LabelsDataProvider;
use Magento\Catalog\Model\ProductFactory;
use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\Registry;
use Magento\Reports\Model\ResourceModel\Product\Collection;
use Amasty\Label\Model\LabelViewer;
use Magento\Store\Model\ScopeInterface;
use Magento\Store\Model\StoreManagerInterface;

class Data extends AbstractHelper
{
    const PATH_FAKE_VIEW_CONFIGURATION_MULTIPLIER = 'fake_view/configuration/multiplier';
    /**
     * @var ProductFactory
     */
    protected $productFactory;
    /**
     * @var Collection
     */
    protected $productCollection;

    /**
     * @var LabelsDataProvider
     */
    protected $labelsDataProvider;
    /**
     * @var Registry
     */
    protected $_registry;
    protected $label;

    protected $labelsFactory;

    /**
     * @var Config
     */
    protected $labelHelper;
    /**
     * @var LabelViewer
     */
    protected $labelViewer;
    /**
     * @var StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @param Context $context
     * @param ProductFactory $productFactory
     * @param Collection $collection
     * @param LabelsDataProvider $labelsDataProvider
     * @param Registry $registry
     * @param LabelsFactory $labelsFactory
     * @param Config $labelHelper
     * @param LabelViewer $labelViewer
     */
    public function __construct(
        Context            $context,
        ProductFactory     $productFactory,
        Collection         $collection,
        LabelsDataProvider $labelsDataProvider,
        Registry           $registry,
        LabelsFactory $labelsFactory,
        Config $labelHelper,
        LabelViewer $labelViewer,
        StoreManagerInterface $storeManager
    ) {
        $this->productFactory     = $productFactory;
        $this->_registry          = $registry;
        $this->labelsDataProvider = $labelsDataProvider;
        $this->productCollection  = $collection;
        $this->labelsFactory = $labelsFactory;
        $this->labelHelper = $labelHelper;
        $this->labelViewer = $labelViewer;
        $this->storeManager = $storeManager;
        parent::__construct($context);
    }

    /**
     * @param $id
     *
     * @return int
     */
    public function getProductCount($idProduct)
    {
        $productObj = $this->productFactory->create()->load($idProduct);
        $this->productCollection->setProductAttributeSetId($productObj->getAttributeSetId());
        $prodData = $this->productCollection->addViewsCount();
        if ($prodData->getSize() > 0) {
            foreach ($prodData as $product) {
                if ($product['entity_id'] == $idProduct) {
                    return (int)$product['views'] * $this->getMultiplierFake();
                }
            }
        }
        return 0;
    }

    /**
     * @param Labels $label
     *
     * @return $this
     */
    public function setLabel(Labels $label)
    {
        $this->label = $label;

        return $this;
    }

    /**
     * @return Labels
     */
    public function getLabel()
    {
        return $this->label;
    }

    /**
     * Get image url with mode and site url
     *
     * @return string
     */
    public function getImageSrc()
    {
        return $this->labelHelper->getImageUrl($this->getLabel()->getProdImg());
    }

    /**
     * @return mixed|null
     */
    public function getCurrentProduct()
    {
        return $this->_registry->registry('current_product');
    }

    /**
     * @param $product
     * @return string
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getLabelProductPage($product, $mode = AbstractLabels::PRODUCT_MODE){
        return $this->labelViewer->renderProductLabel($product, $mode);
    }

    /**
     * @return mixed
     */
    public function getFrontendSettings()
    {
        return $this->getLabel()->getMode();
    }

    /**
     * @return string
     */
    public function getLabelKey()
    {
        $_product = $this->getCurrentProduct();
        $mode     = $this->getFrontendSettings() === 'cat' ? 'cat' : 'prod';
        return $this->getLabel()->getLabelId() . '-' . $_product->getId() . '-' . $mode;
    }

    /**
     * @return string
     */
    public function getLabelText()
    {
        return $this->getLabel()->getText();
    }

    /**
     * @return int
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getStoreId(){
        return $this->storeManager->getStore()->getStoreId();
    }

    /**
     * @return mixed
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getMultiplierFake(){
        $multiplier =  $this->scopeConfig->getValue(
            self::PATH_FAKE_VIEW_CONFIGURATION_MULTIPLIER,
            ScopeInterface::SCOPE_STORE,
            $this->getStoreId()
        );
        return $multiplier ?: 1;
    }
}
