<?php
/**
 * Created By : RH
 */
namespace Ecommage\RelatedCategory\Block\Adminhtml;

class AssignSlider extends \Magento\Backend\Block\Template
{
    /**
     * Block template
     *
     * @var string
     */
    protected $_template = 'Ecommage_RelatedCategory::related/assign_categories_slider.phtml';
    /**
     * @var \Magento\Catalog\Block\Adminhtml\Category\Tab\Product
     */
    protected $blockGrid;
    /**
     * @var \Magento\Framework\Registry
     */
    protected $registry;
    /**
     * @var \Magento\Framework\Json\EncoderInterface
     */
    protected $jsonEncoder;
    /**
     * @var \Ecommage\SliderCategoryCollection\Model\SliderFactory
     */
    protected $sliderFactory;
    /**
     * @var \Ecommage\RelatedCategory\Model\ResourceModel\CategorySlider\CollectionFactory
     */
    protected $categorySliderCollectionFactory;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\Json\EncoderInterface $jsonEncoder
     * @param \Ecommage\SliderCategoryCollection\Model\SliderFactory $sliderFactory
     * @param \Ecommage\RelatedCategory\Model\ResourceModel\CategorySlider\CollectionFactory $categorySliderCollectionFactory
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Json\EncoderInterface $jsonEncoder,
        \Ecommage\SliderCategoryCollection\Model\SliderFactory $sliderFactory,
        \Ecommage\RelatedCategory\Model\ResourceModel\CategorySlider\CollectionFactory $categorySliderCollectionFactory,
        array $data = []
    ) {
        $this->registry = $registry;
        $this->jsonEncoder = $jsonEncoder;
        $this->sliderFactory = $sliderFactory;
        $this->categorySliderCollectionFactory = $categorySliderCollectionFactory;
        parent::__construct($context, $data);
    }
    /**
     * Retrieve instance of grid block
     *
     * @return \Magento\Framework\View\Element\BlockInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getBlockGrid()
    {
        if (null === $this->blockGrid) {
            $this->blockGrid = $this->getLayout()->createBlock(
                'Ecommage\RelatedCategory\Block\Adminhtml\Tab\SliderGrid',
                'related.slider.grid'
            );
        }
        return $this->blockGrid;
    }
    /**
     * Return HTML of grid block
     *
     * @return string
     */
    public function getGridHtml()
    {
        return $this->getBlockGrid()->toHtml();
    }

    /**
     * @return string
     */
    public function getSliderJson()
    {
        $entity_id = $this->getRequest()->getParam('id');
        $postCollection = $this->categorySliderCollectionFactory->create();
        $postCollection->addFieldToFilter('category_id', ['eq' => $entity_id]);
        $result = [];
        if (!empty($postCollection)) {
            foreach ($postCollection as $rhPosts) {
                $result[] = $rhPosts['slider_ctg_id'];
            }

            return $this->jsonEncoder->encode($result);
        }
        return '{}';
    }

    /**
     * @return mixed|null
     */
    public function getItem()
    {
        return $this->registry->registry('my_item');
    }
}
