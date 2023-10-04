<?php

namespace Ecommage\CustomerReview\Block\Widget;

use Magento\Framework\View\Element\Template;
use Magento\Widget\Block\BlockInterface;
use Ecommage\CustomerReview\Model\ResourceModel\Review\CollectionFactory;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\UrlInterface;
use Magento\Directory\Model\RegionFactory;
use Magento\Directory\Model\ResourceModel\Region;
use Amasty\PromoBanners\Model\Rule;
use Amasty\PromoBanners\Model\RuleFactory;
use Magento\Cms\Model\Template\FilterProvider;
use Amasty\PromoBanners\Block\Banner\ProductListing;

/**
 * Class Review
 *
 * @package Ecommage\CustomerReview\Block\Widget
 */
class Review extends Template implements BlockInterface
{
    protected $gender;
    /**
     * @var string
     */
        protected $_template = 'Ecommage_CustomerReview::widget/customer/review.phtml';
    /**
     * @var CollectionFactory
     */
    protected $_collection;
    /**
     * @var StoreManagerInterface
     */
    protected $_storeManager;
    /**
     * @var RegionFactory
     */
    protected $_regionFactory;
    /**
     * @var RegionResource
     */
    protected $_regionResource;
    /**
     * @var RuleFactory
     */
    protected $_bannerFactory;
    /**
     * @var FilterProvider
     */
    protected $_contentProcessor;

    /**
     * Review constructor.
     *
     * @param Template\Context      $context
     * @param CollectionFactory     $collection
     * @param StoreManagerInterface $storeManager
     * @param RegionFactory         $regionFactory
     * @param RuleFactory           $bannerFactory
     * @param FilterProvider        $contentProcessor
     * @param array                 $data
     */
    public function __construct(
        Template\Context $context,
        CollectionFactory $collection,
        StoreManagerInterface $storeManager,
        RegionFactory $regionFactory,
        Region $_regionResource,
        RuleFactory $bannerFactory,
        FilterProvider $contentProcessor,
        array $data = []
    )
    {
        $this->_regionResource = $_regionResource;
        $this->_collection    = $collection;
        $this->_storeManager  = $storeManager;
        $this->_regionFactory = $regionFactory;
        $this->_bannerFactory = $bannerFactory;
        $this->_contentProcessor = $contentProcessor;
        parent::__construct($context, $data);
    }

    /**
     * @return array|mixed|null
     */
    public function getBannerProducts()
    {
        return $this->getBanner()->getData('product_collection') ?: [];
    }

    /**
     * @return array
     */
    public function getBannerShowProducts()
    {
        return $this->getBanner()->getData('show_products') ?: [];
    }

    /**
     * @return string
     */
    protected function _toHtml()
    {
        $products = [];
        if ($this->getBannerShowProducts()) {
            $products = $this->getBannerProducts();
        }
        $listingBlock = $this->getLayout()
                             ->createBlock(ProductListing::class)
                             ->setData('products', $products);

        $this->setChild('product_listing', $listingBlock);

        return parent::_toHtml();
    }

    /**
     * @return mixed
     */
    public function getCollection()
    {
        return $this->_collection->create()->addFieldToFilter('is_active', true);
    }

    /**
     * @param $fileName
     *
     * @return string
     */
    public function getFileInfo($fileName, $path)
    {
        return $this->_storeManager->getStore()->getBaseUrl(UrlInterface::URL_TYPE_MEDIA).$path.$fileName;
    }

    /**
     * @param $gender
     * @SuppressWarnings(PHPMD.ElseExpression)
     * @return mixed
     */
    public function getGender($gender)
    {
        if ($gender === 0) {
            $result = __('Mr');
        } else {
            $result = __('Ms');
        }
        return $result;
    }

    /**
     * @param $cityId
     *
     * @return mixed
     */
    public function getCity($cityId)
    {
        $model = $this->_regionFactory->create();
        $this->_regionResource->load($model, $cityId);
        return $model->getDefaultName();
    }

    /**
     * @return Rule
     */
    public function getBanner()
    {
        $bannerId = $this->getChoosePromoBanner();
        return $this->_bannerFactory->create()->load($bannerId);
    }

    /**
     * @return string
     * @throws LocalizedException
     * @throws NoSuchEntityException
     */
    public function getBannerText()
    {
        $baseMediaUrl = $this->_storeManager->getStore()->getBaseUrl(UrlInterface::URL_TYPE_MEDIA);
        switch ($this->getBanner()->getBannerType()) {
            case Rule::TYPE_CMS:
                $blockName = $this->getBanner()->getCmsBlock();
                if ($blockName) {
                    $blockHtml = $this->getLayout()
                                      ->createBlock(Block::class)
                                      ->setBlockId($blockName)
                                      ->toHtml();

                    return $blockHtml;
                }
                break;
            case Rule::TYPE_IMAGE:
                $title          = __($this->getBanner()->getBannerTitle());
                $imageUrl       = $baseMediaUrl . 'amasty/ampromobanners/' . $this->getBanner()->getBannerImg();
                $imageMobileUrl = $imageUrl;
                if ($this->getBanner()->getBannerImgMobile()) {
                    $imageMobileUrl = $baseMediaUrl . 'amasty/ampromobanners/' . $this->getBanner()->getBannerImgMobile();
                }

                $bannerId = $this->getBanner()->getId();

                $html = "";

                if ($title->getText()) {
                    $titleText = "<div class='title'>$title</div>";
                } elseif (!$title->getText()) {
                    $titleText = "";
                }

                $picture = "<picture>
                              <source media='(max-width: 767px)' srcset='$imageMobileUrl'>
                              <source media='(min-width: 768px)' srcset='$imageUrl'>
                              <img class='visible-lg' id='promotions-banner-$bannerId' src='$imageUrl' alt='$title'>
                            </picture>
                            $titleText
                            ";
                if ($link = $this->getBanner()->getBannerLink()) {
                    $html = "<a href='$link'>$picture</a>";
                } elseif (!$this->getBanner()->getBannerLink()) {
                    $html .= $picture;
                }

                return $html;
            case Rule::TYPE_HTML:
            default:
                return $this->_contentProcessor->getPageFilter()->filter($this->getBanner()->getHtmlText());
        }

        return '';
    }
}
