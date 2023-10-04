<?php
namespace Ecommage\ViewProduct\Helper;

use Amasty\AdvancedReview\Block\Images;
use Amasty\AdvancedReview\Model\ResourceModel\Images\Collection;
use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Amasty\AdvancedReview\Helper\Config;
use Magento\Framework\UrlInterface;
use Magento\Framework\Json\EncoderInterface;
use Magento\Framework\Registry;
use Amasty\AdvancedReview\Helper\ImageHelper;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Review\Model\ResourceModel\Review\CollectionFactory as ReviewCollection;

class ImageReview extends AbstractHelper
{
    /**
     * @var \Magento\Framework\Json\EncoderInterface
     */
    private $jsonEncoder;

    /**
     * @var \Amasty\AdvancedReview\Helper\Config
     */
    private $configHelper;

    /**
     * @var \Amasty\AdvancedReview\Helper\ImageHelper
     */
    private $imageHelper;

    /**
     * @var Registry
     */
    protected $registry;

    /**
     * @var StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var ReviewCollection
     */
    protected $reviewCollection;

    /**
     * @param Context $context
     * @param Config $configHelper
     * @param ImageHelper $imageHelper
     * @param EncoderInterface $jsonEncoder
     * @param Registry $registry
     * @param StoreManagerInterface $storeManager
     * @param ProductFactory $productFactory
     * @param ReviewCollection $reviewCollection
     */
    public function __construct(Context $context,
                                Config $configHelper,
                                ImageHelper $imageHelper,
                                EncoderInterface $jsonEncoder,
                                Registry $registry,
                                StoreManagerInterface $storeManager,
                                ReviewCollection $reviewCollection
    )
    {
        parent::__construct($context);
        $this->jsonEncoder = $jsonEncoder;
        $this->configHelper = $configHelper;
        $this->imageHelper = $imageHelper;
        $this->registry = $registry;
        $this->storeManager = $storeManager;
        $this->reviewCollection = $reviewCollection;
    }

    /**
     * @param $item
     * @return string
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getResizedImagePath($path)
    {
        return $this->imageHelper->resize($path, $this->configHelper->getReviewImageWidth() * 2);
    }

    /**
     * @return mixed
     */
    public function getCurrentProduct()
    {
        $productId = $this->registry->registry('current_product')->getId();
        return $productId;
    }

    /**
     * @return \Magento\Review\Model\ResourceModel\Review\Collection
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     *
     */
    public function getReviewProduct()
    {
        $currentStoreId = $this->storeManager->getStore()->getId();
        $collection = $this->reviewCollection->create()->addStoreFilter($currentStoreId)
            ->addStatusFilter(\Magento\Review\Model\Review::STATUS_APPROVED)
            ->addFieldToFilter('entity_pk_value', $this->getCurrentProduct());
        $collection->getSelect()->joinLeft(
                ['review_image' => $collection->getTable('amasty_advanced_review_images')],
                'main_table.review_id = review_image.review_id',
                ['image_path' => 'review_image.path'])
            ->where('review_image.path != ?', 'null')
            ->order('review_image.image_id DESC')
            ->limit(10);
        return $collection;
    }

    /**
     * @param $name
     * @return string
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getFullPath($name)
    {
        $path = $this->storeManager->getStore()->getBaseUrl(UrlInterface::URL_TYPE_MEDIA);
        $path = trim($path, '/');
        $name = trim($name, '/');
        $path .= '/amasty/review/';
        return $path . $name;
    }
}
