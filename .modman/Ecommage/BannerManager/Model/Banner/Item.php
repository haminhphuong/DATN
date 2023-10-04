<?php

namespace Ecommage\BannerManager\Model\Banner;

use Magento\Framework\Data\Collection\AbstractDb;
use Magento\Framework\DataObject\IdentityInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Model\AbstractModel;
use Magento\Framework\Model\Context;
use Magento\Framework\Model\ResourceModel\AbstractResource;
use Magento\Framework\Registry;
use Magento\Framework\UrlInterface;
use Magento\Store\Model\StoreManagerInterface;
use Ecommage\BannerManager\Api\Data\ItemInterface;
use Ecommage\BannerManager\Model\BannerFactory;
use Ecommage\BannerManager\Model\System\Config\MediaType;

/**
 * Class Item
 *
 * @package Ecommage\BannerManager\Model\Banner
 */
class Item extends AbstractModel implements ItemInterface, IdentityInterface
{
    const CACHE_TAG = 'banner_item';
    /**
     * @var string
     */
    protected $_eventPrefix = self::CACHE_TAG;
    /**
     * @var string
     */
    protected $_eventObject = 'banner_item';
    /**
     * @var string
     */
    protected $_idFieldName = 'entity_id';
    /**
     * @var StoreManagerInterface
     */
    protected $_storeManager;
    /**
     * @var BannerFactory
     */
    protected $_bannerFactory;
    /**
     * Banner instance
     *
     * @var \Ecommage\BannerManager\Model\Banner
     */
    protected $_banner = null;

    /**
     * Item constructor.
     *
     * @param Context               $context
     * @param Registry              $registry
     * @param StoreManagerInterface $storeManager
     * @param AbstractResource|null $resource
     * @param AbstractDb|null       $resourceCollection
     * @param array                 $data
     */
    public function __construct(
        Context               $context,
        Registry              $registry,
        BannerFactory         $bannerFactory,
        StoreManagerInterface $storeManager,
        AbstractResource      $resource = null,
        AbstractDb            $resourceCollection = null,
        array                 $data = []
    ) {
        $this->_storeManager  = $storeManager;
        $this->_bannerFactory = $bannerFactory;
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
    }

    /**
     * @inheritDoc
     */
    public function getIdentities()
    {
        return [self::CACHE_TAG . '_' . $this->getId()];
    }

    /**
     * Returns image url
     *
     * @param string $attributeCode
     *
     * @return bool|string
     * @throws LocalizedException
     */
    public function getImageUrl($attributeCode = 'image_desktop')
    {
        $url   = false;
        $image = $this->getData($attributeCode);
        if ($image) {
            if (!is_string($image)) {
                throw new LocalizedException(__('Something went wrong while getting the image url.'));
            }

            $url = $this->_storeManager->getStore()->getBaseUrl(UrlInterface::URL_TYPE_MEDIA) . $image;
        }
        return $url;
    }

    /**
     * Init
     */
    protected function _construct() // phpcs:ignore PSR2.Methods.MethodDeclaration
    {
        $this->_init(\Ecommage\BannerManager\Model\ResourceModel\Banner\Item::class);
    }

    /**
     * @return int|null
     */
    public function getBannerId()
    {
        return $this->getData(self::BANNER_ID);
    }

    /**
     * @return string|null
     */
    public function getTitle()
    {
        return $this->getData(self::TITLE);
    }

    /**
     * @return string|null
     */
    public function getImageDesktop()
    {
        return $this->getData(self::IMAGE_DESKTOP);
    }

    /**
     * @return string|null
     */
    public function getImageMobile()
    {
        return $this->getData(self::IMAGE_MOBILE);
    }

    /**
     * @return string|null
     */
    public function getLink()
    {
        return $this->getData(self::LINK);
    }

    /**
     * @return string|null
     */
    public function getDescription()
    {
        return $this->getData(self::DESCRIPTION);
    }

    /**
     * @return array
     */
    public function getOptions()
    {
        $options = $this->getData(self::OPTIONS);
        if (!empty($options) && is_string($options)) {
            $options = json_decode($options, true);
            $this->setOptions($options);
        }

        return (array)$options;
    }

    /**
     * @return string|null
     */
    public function getClassItem()
    {
        return $this->getData(self::CLASS_ITEM);
    }

    /**
     * @return string|null
     */
    public function getCreationTime()
    {
        return $this->getData(self::CREATION_TIME);
    }

    /**
     * @return string|null
     */
    public function getUpdateTime()
    {
        return $this->getData(self::UPDATE_TIME);
    }

    /**
     * @return string|null
     */
    public function getStartDate()
    {
        return $this->getData(self::START_DATE);
    }

    /**
     * @return string|null
     */
    public function getEndDate()
    {
        return $this->getData(self::END_DATE);
    }

    /**
     * @return bool|null
     */
    public function isActive()
    {
        return (bool)$this->getData(self::IS_ACTIVE);
    }

    /**
     * @return int
     */
    public function getPosition()
    {
        return (int)$this->getData(self::POSITION);
    }

    /**
     * @param int $id
     *
     * @return mixed|\Ecommage\BannerManager\Api\Data\BannerInterface
     */
    public function setBannerId($id)
    {
        return $this->getData(self::BANNER_ID, $id);
    }

    /**
     * @param string $title
     *
     * @return ItemInterface
     */
    public function setTitle($title)
    {
        return $this->getData(self::TITLE, $title);
    }

    /**
     * @param string $image
     *
     * @return ItemInterface|Item
     */
    public function setImageDesktop($image)
    {
        return $this->setData(self::IMAGE_DESKTOP, $image);
    }

    /**
     * @param string $image
     *
     * @return ItemInterface|Item
     */
    public function setImageMobile($image)
    {
        return $this->setData(self::IMAGE_MOBILE, $image);
    }

    /**
     * @param string $link
     *
     * @return ItemInterface|Item
     */
    public function setLink($link)
    {
        return $this->setData(self::LINK, $link);
    }

    /**
     * @param string $description
     *
     * @return ItemInterface|Item
     */
    public function setDescription($description)
    {
        return $this->setData(self::DESCRIPTION, $description);
    }

    /**
     * @param array $options
     *
     * @return ItemInterface|Item
     */
    public function setOptions($options)
    {
        return $this->setData(self::OPTIONS, $options);
    }

    /**
     * @param string $class
     *
     * @return ItemInterface|Item
     */
    public function setClassItem($class)
    {
        return $this->setData(self::CLASS_ITEM, $class);
    }

    /**
     * @param string $creationTime
     *
     * @return ItemInterface|Item
     */
    public function setCreationTime($creationTime)
    {
        return $this->setData(self::CREATION_TIME, $creationTime);
    }

    /**
     * @param string $updateTime
     *
     * @return ItemInterface|Item
     */
    public function setUpdateTime($updateTime)
    {
        return $this->setData(self::CREATION_TIME, $updateTime);
    }

    /**
     * @param string $date
     *
     * @return ItemInterface|Item
     */
    public function setStartDate($date)
    {
        return $this->getData(self::START_DATE, $date);
    }

    /**
     * @param string $date
     *
     * @return ItemInterface|Item
     */
    public function setEndDate($date)
    {
        return $this->getData(self::END_DATE, $date);
    }

    /**
     * @param bool|int $isActive
     *
     * @return ItemInterface|Item
     */
    public function setIsActive($isActive)
    {
        return $this->setData(self::IS_ACTIVE, $isActive);
    }

    /**
     * @param int $position
     *
     * @return ItemInterface|Item
     */
    public function setPosition($position)
    {
        return $this->setData(self::POSITION, $position);
    }

    /**
     * Retrieve banner model object
     *
     * @return \Ecommage\BannerManager\Model\Banner
     */
    public function getBanner()
    {
        if ($this->_banner === null && ($bannerId = $this->getBannerId())) {
            $banner = $this->_bannerFactory->create();
            $banner->load($bannerId);
            $this->setBanner($banner);
        }
        return $this->_banner;
    }

    /**
     * @param $banner
     *
     * @return $this
     */
    public function setBanner($banner)
    {
        $this->_banner = $banner;
        return $this;
    }

    /**
     * @return Item
     * @throws \Magento\Framework\Validator\Exception
     */
    public function validate()
    {
        return parent::validateBeforeSave();
    }

    /**
     * @return Item
     */
    public function beforeSave()
    {
        //convert options to string
        $options = $this->getData(self::OPTIONS);
        if (is_array($options)) {
            $this->setData(self::OPTIONS, json_encode($options));
        }

        return parent::beforeSave();
    }
}
