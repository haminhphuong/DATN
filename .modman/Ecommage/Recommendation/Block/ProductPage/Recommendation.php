<?php
namespace Ecommage\Recommendation\Block\ProductPage;
use Magento\Catalog\Model\Product;
use Magento\Catalog\Pricing\Price\FinalPrice;
use Magento\Framework\Registry;
use Magento\Customer\Model\Session;
use \Magento\Catalog\Block\Product\Context;
use Magento\Catalog\Block\Product\AbstractProduct;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory;
use Ecommage\Recommendation\Helper\Data;

class Recommendation extends AbstractProduct
{
    const PEOPLE_ALSO_VIEWED = 'people_also_viewed';
    const SIMILAR_PRODUCT = 'similar_product';
    /**
     * @var Registry
     */
    protected $registry;
    /**
     * @var Session
     */
    protected $session;
    /**
     * @var
     */
    protected $collectionFactory;
    /**
     * @var Data
     */
    protected $helper;
    /**
     * @var string
     */
    protected $_template = 'Ecommage_Recommendation::recommendation/product-page.phtml';

    /**
     * @param Context $context
     * @param array $data
     * @param Registry $registry
     * @param Session $session
     */
    public function __construct
    (
        Context $context,
        array $data = [],
        Registry $registry,
        Session $session,
        CollectionFactory $collectionFactory,
        Data $helper
    )
    {
        parent::__construct($context, $data);
        $this->registry = $registry;
        $this->session = $session;
        $this->collectionFactory = $collectionFactory;
        $this->helper = $helper;
        $this->session->setFlag(true);
    }

    /**
     * @return mixed|null
     */
    public function getProduct()
    {
        return $this->registry->registry('product');
    }

    /**
     * @param $flag
     * @return mixed
     */
    public function setFlag($flag = true){
        return $this->session->setFlag($flag);
    }

    /**
     * @return mixed
     */
    public function getFlag(){
        return $this->session->getFlag();
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
     * @return mixed|null
     */
    public function getCollection(){
        $ids = $this->helper->getProductIds();
        $collection = null;
        if($ids){
            $collection = $this->collectionFactory->create()->addFieldToFilter('entity_id',['in'=>$ids])->addAttributeToSelect('*');
            $this->setFlag(false);
            if($this->helper->getType() == 'people_also_viewed'){
                $this->helper->setTitle($this->helper->titlePeopleAlsoViewed());
            }
            if($this->helper->getType() == 'similar_product'){
                $this->helper->setTitle($this->helper->titleSimilarProduct());
            }
        }
        return $collection;
    }

}
