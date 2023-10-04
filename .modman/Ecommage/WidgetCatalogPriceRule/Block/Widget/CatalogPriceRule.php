<?php

namespace Ecommage\WidgetCatalogPriceRule\Block\Widget;

use Magento\Catalog\Block\Product\AbstractProduct;
use Magento\Catalog\Model\Product;
use Magento\Catalog\Model\Product\Attribute\Source\Status;
use Magento\Catalog\Model\Product\Visibility;
use Magento\Catalog\Pricing\Price\FinalPrice;
use Magento\Framework\App\ActionInterface;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\Url\EncoderInterface;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Widget\Block\BlockInterface;
use Magento\Catalog\Block\Product\Context;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory;
use Magento\Catalog\Model\ProductFactory;
use Magento\Catalog\Helper\Image;
use Magento\Framework\App\ResourceConnection;
use Magento\Framework\Data\Form\FormKey;

class CatalogPriceRule extends AbstractProduct implements BlockInterface
{
    protected $_template = "widget/showdata.phtml";
    /**
     * @var CollectionFactory
     */
    protected $productCollection;
    /**
     * @var ProductFactory
     */
    protected $productFactory;
    /**
     * @var Image
     */
    protected $imageHelper;
    /**
     * @var EncoderInterface|mixed
     */
    protected $urlEncoder;
    /**
     * @var ResourceConnection
     */
    protected $resource;
    /**
     * @var StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @param EncoderInterface|null $urlEncoder
     * @param Context $context
     * @param CollectionFactory $productCollection
     * @param ProductFactory $productFactory
     * @param Image $imageHelper
     * @param ResourceConnection $resource
     * @param StoreManagerInterface $storeManager
     * @param array $data
     */
    public function __construct(
        EncoderInterface $urlEncoder = null,
        Context $context,
        CollectionFactory $productCollection,
        ProductFactory $productFactory,
        Image $imageHelper,
        ResourceConnection $resource,
        StoreManagerInterface $storeManager,
        FormKey $formKey,
        array $data = []
    )
    {
        $this->productFactory = $productFactory;
        $this->imageHelper = $imageHelper;
        $this->productCollection = $productCollection;
        $this->resource = $resource;
        $this->storeManager = $storeManager;
        $this->formKey = $formKey;
        $this->urlEncoder = $urlEncoder ?: ObjectManager::getInstance()->get(EncoderInterface::class);
        parent::__construct($context, $data);
    }

    /**
     * @return \Magento\Catalog\Model\ResourceModel\Product\Collection
     */
    public function getSaleProduct(){
        $collection = $this->getProductData();
        $collection->getSelect()
            ->joinLeft(
                ['c' => $collection->getTable('cataloginventory_stock_item')],
                'c.product_id = e.entity_id',
                [
                    'stock_id' => 'c.stock_id',
                    'qty' => 'c.qty',
                    'is_in_stock' => 'c.is_in_stock',
                ]
        );
        $collection->getSelect()
            ->joinLeft(
                ['p' => $collection->getTable("catalog_product_index_price")],
                'p.entity_id = e.entity_id',
                [
                    'saving_price' => new \Zend_Db_Expr('p.price - p.final_price'),
                ]
        )->group('p.entity_id')->order('saving_price DESC');
        $collection->getSelect()->where("c.is_in_stock = 1 AND c.stock_id = 1");

        return $collection;
    }
    /**
     * @return \Magento\Catalog\Model\ResourceModel\Product\Collection
     */
    public function getLimitedProduct(){
        $product = $this->getProductData();
        return $product->setPageSize($this->getSizePage())->setCurPage($this->getPage());
    }

    /**
     * @return mixed
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getProductData(){
        /** @var \Magento\Catalog\Model\ResourceModel\Product\Collection $collection */
        $collection = $this->productCollection->create();
        $collection->addAttributeToSelect('*');
        $collection->addAttributeToFilter('sale',1);
        $collection->addAttributeToFilter('status', Status::STATUS_ENABLED);
        $collection->addStoreFilter($this->storeManager->getStore());
        $collection->addAttributeToFilter('visibility', Visibility::VISIBILITY_BOTH);
        $collection->addCategoriesFilter(['in' => $this->getData('show_all')]);
        $collection->joinField('stock_item', 'cataloginventory_stock_item', 'qty', 'product_id=entity_id', 'qty > 0');
        $collection->addFinalPrice();
        $collection->getSelect()->where('price_index.final_price > 0');

        return $collection;
    }

    /**
     * @return int
     */
    public function countProduct(){
        return $this->getProductData()->getSize();
    }

    /**
     * @return array|mixed|null
     */
    protected function getSizePage(){
        $num = $this->getData("number_product_show");
        if (!$this->hasData('page_size')) {
            $this->setData('page_size', $num);
        }

        return $this->getData('page_size') ;
    }

    /**
     * @return array|mixed|null
     */
    protected function getPage(){
        if (!$this->hasData('current_page')) {
            $this->setData('current_page', 1);
        }
        return $this->getData('current_page');
    }
    /**
     * Get post parameters.
     *
     * @param Product $product
     * @return array
     */
    public function getAddToCartPostParams(Product $product)
    {
        $url = $this->getAddToCartUrl($product);
        return [
            'action' => $url,
            'data' => [
                'product' => $product->getEntityId(),
                ActionInterface::PARAM_NAME_URL_ENCODED => $this->urlEncoder->encode($url),
            ]
        ];
    }

    /**
     * @param \Magento\Catalog\Model\Product $product
     * @return string
     * @throws NoSuchEntityException
     */
    public function getProductImage($product)
    {
        return $this->imageHelper->init($product, 'product_thumbnail_image')
            ->resize($this->getImageWidth(), $this->getImageHight())
            ->getUrl();
    }
    /**
     * @return array|mixed|null
     */
    public function getImageHight()
    {
        if (!$this->hasData('imageheight')) {
            $this->setData('imageheight', 327);
        }

        return $this->getData('imageheight');
    }
    /**
     * @return array|mixed|null
     */
    public function getImageWidth()
    {
        if (!$this->hasData('imagewidth')) {
            $this->setData('imagewidth', 300);
        }
        return $this->getData('imagewidth');
    }
    /**
     * Return HTML block with price
     *
     * @param Product $product
     * @return string
     */
    public function getProductPrice(\Magento\Catalog\Model\Product $product)
    {
        return $this->getProductPriceHtml(
            $product,
            \Magento\Catalog\Pricing\Price\FinalPrice::PRICE_CODE,
            \Magento\Framework\Pricing\Render::ZONE_ITEM_LIST
        );
    }

    /**
     * @inheritdoc
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    public function getProductPriceHtml(
        Product $product,
                $priceType = null,
                $renderZone = \Magento\Framework\Pricing\Render::ZONE_ITEM_LIST,
        array $arguments = []
    ) {
        if (!isset($arguments['zone'])) {
            $arguments['zone'] = $renderZone;
        }
        $arguments['price_id'] = isset($arguments['price_id'])
            ? $arguments['price_id']
            : 'old-price-' . $product->getId() . '-' . $priceType;
        $arguments['include_container'] = isset($arguments['include_container'])
            ? $arguments['include_container']
            : true;
        $arguments['display_minimal_price'] = isset($arguments['display_minimal_price'])
            ? $arguments['display_minimal_price']
            : true;

        /** @var \Magento\Framework\Pricing\Render $priceRender */
        $priceRender = $this->getLayout()->getBlock('product.price.render.default');
        if (!$priceRender) {
            $priceRender = $this->getLayout()->createBlock(
                \Magento\Framework\Pricing\Render::class,
                'product.price.render.default',
                ['data' => ['price_render_handle' => 'catalog_product_prices']]
            );
        }

        $price = $priceRender->render(
            FinalPrice::PRICE_CODE,
            $product,
            $arguments
        );

        return $price;
    }

    /**
     * @return string
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getKey(){
        return $this->formKey->getFormKey();
    }
}
