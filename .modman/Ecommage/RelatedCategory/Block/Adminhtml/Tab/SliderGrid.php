<?php
/**
 * Created By : RH
 */

namespace Ecommage\RelatedCategory\Block\Adminhtml\Tab;

use Ecommage\SliderCategoryCollection\Model\ResourceModel\Slider\CollectionFactory;;
use Ecommage\RelatedCategory\Model\ResourceModel\CategorySliderFactory;
use Exception;
use Magento\Backend\Block\Template\Context;
use Magento\Backend\Block\Widget\Grid\Column;
use Magento\Backend\Block\Widget\Grid\Extended;
use Magento\Backend\Helper\Data;
use Magento\Framework\Exception\FileSystemException;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Registry;
use Magento\Store\Model\StoreManagerInterface;

class SliderGrid extends Extended
{
    /**
     * @var Registry
     */
    protected $coreRegistry = null;
    /**
     * @var CollectionFactory
     */
    protected $sliderCategoryCollectionFactory;
    /**
     * @var CategorySliderFactory
     */
    protected $categorySliderFactory;

    /**
     * @param Context $context
     * @param Data $backendHelper
     * @param CollectionFactory $sliderCategoryCollectionFactory
     * @param CategorySliderFactory $categorySliderFactory
     * @param Registry $coreRegistry
     * @param StoreManagerInterface $storeManager
     * @param array $data
     */
    public function __construct(
        Context                            $context,
        Data                                       $backendHelper,
        CollectionFactory           $sliderCategoryCollectionFactory,
        CategorySliderFactory $categorySliderFactory,
        Registry                                        $coreRegistry,
        StoreManagerInterface                         $storeManager,
        array                                                              $data = []
    )
    {
        $this->categorySliderFactory = $categorySliderFactory;
        $this->postCollectionFactory = $sliderCategoryCollectionFactory;
        $this->coreRegistry = $coreRegistry;
        $this->_storeManager = $storeManager;
        parent::__construct($context, $backendHelper, $data);
    }

    /**
     * @throws FileSystemException
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setId('related_grid_post');
        $this->setDefaultSort('slider_ctg_id');
        $this->setDefaultDir('DESC');
        $this->setUseAjax(true);
//        $this->setPagerVisibility(true);
//        $this->setFilterVisibility(true);
    }

    /**
     * @return PostsGrid
     */
    protected function _prepareCollection()
    {
        $collection = $this->postCollectionFactory->create();
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    /**
     * @param Column $column
     * @return $this|PostsGrid
     * @throws LocalizedException
     */
    protected function _addColumnFilterToCollection($column)
    {
        if ($column->getId() == 'in_slider_ctg') {
            $postIds = $this->getSelectedSlider();
            if (empty($postIds)) {
                $postIds = 0;
            }

            if ($column->getFilter()->getValue()) {
                $this->getCollection()->addFieldToFilter('slider_ctg_id', ['in' => $postIds]);
            } elseif (!$column->getFilter()->getValue()) {
                if ($postIds) {
                    $this->getCollection()->addFieldToFilter('slider_ctg_id', ['in' => $postIds]);
                }
            }
        } elseif ($column->getId() != 'in_slider_ctg') {
            parent::_addColumnFilterToCollection($column);
        }
        return $this;
    }

    /**
     *
     */
    protected function _preparePage()
    {
        $this->getCollection()->setPageSize(10)->setCurPage(1);
    }

    /**
     * @return string
     */
    public function getGridUrl()
    {
        return $this->getUrl('related_categories_slider/index/grids', ['_current' => true]);
    }

    /**
     * @return SliderGrid
     * @throws Exception
     */
    protected function _prepareColumns()
    {
        $this->addColumn(
            'in_slider_ctg',
            [
                'index' => 'slider_ctg_id',
                'required' => true,
                'align' => 'center',
                'type' => 'checkbox',
                'html_name' => 'slider_ctg_ids[]',
                'field_name' => 'slider_ctg_ids[]',
                'values' => $this->getSelectedSlider()
            ]
        );
        $this->addColumn(
            'slider_ctg_id',
            [
                'header' => __('ID'),
                'width' => '50px',
                'index' => 'slider_ctg_id',
                'type' => 'number',
                'header_css_class' => 'col-id',
                'column_css_class' => 'col-id'
            ]
        );
        $this->addColumn(
            'image',
            [
                'header' => __('Image'),
                'index' => 'image',
                'header_css_class' => 'col-type',
                'column_css_class' => 'col-type',
                'renderer'  => '\Ecommage\RelatedCategory\Block\Adminhtml\Grid\Renderer\Image',
            ]
        );

        $this->addColumn(
            'text_link',
            [
                'header' => __('Text Link'),
                'index' => 'text_link',
                'header_css_class' => 'col-type',
                'column_css_class' => 'col-type',
            ]
        );
        return parent::_prepareColumns();
    }

    /**
     * @return \Magento\Catalog\Model\Category
     */
    public function getCurrentCategory()
    {
        return $this->coreRegistry->registry('current_category');
    }

    /**
     * @return array
     */
    public function getSelectedSlider($json = false)
    {
        /* @var $category \Magento\Catalog\Model\Category */
        $category = $this->getCurrentCategory();
        if (!$category) {
            return null;
        }
        return $this->categorySliderFactory->create()->getPostByCategory($category);
    }
}
