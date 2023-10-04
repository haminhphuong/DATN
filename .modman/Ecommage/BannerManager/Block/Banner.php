<?php

namespace Ecommage\BannerManager\Block;

use Magento\Framework\View\Element\Template;
use Ecommage\BannerManager\Model\Banner\Item;
use Ecommage\BannerManager\Model\System\Config\MediaType;
use Ecommage\BannerManager\Model\System\Config\Type;

/**
 * Banner content block
 */
class Banner extends Template implements \Magento\Framework\DataObject\IdentityInterface
{
    /**
     * Banner item factory
     *
     * @var \Ecommage\BannerManager\Model\Banner\ItemFactory
     */
    protected $_bannerItemFactory;
    /**
     * Banner factory
     *
     * @var \Ecommage\BannerManager\Model\BannerFactory
     */
    protected $_bannerFactory;

    /**
     * Construct
     *
     * @param \Magento\Framework\View\Element\Context    $context
     * @param \Magento\Cms\Model\Template\FilterProvider $filterProvider
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Ecommage\BannerManager\Model\BannerFactory     $bannerFactory
     * @param array                                      $data
     */
    public function __construct(
        \Ecommage\BannerManager\Model\BannerFactory $bannerFactory,
        \Magento\Framework\View\Element\Template\Context $context,
        \Ecommage\BannerManager\Model\Banner\ItemFactory $bannerItemFactory,
        array $data = []
    ) {
        $this->_bannerItemFactory = $bannerItemFactory;
        $this->_bannerFactory     = $bannerFactory;
        parent::__construct($context, $data);
    }

    /**
     * @inheritDoc
     */
    public function _construct()
    {
        parent::_construct();
        $this->addData(
            ['cache_lifetime' => 86400]
        );
    }

    /**
     * @return mixed|null
     */
    public function getBannerId()
    {
        if ($bannerId = $this->getData('banner_id')) {
            return $bannerId;
        }

        $selectedBanner = $this->getBannerSelected();
        return $this->_scopeConfig->getValue($selectedBanner);
    }

    /**
     * @return \Ecommage\BannerManager\Model\Banner|null
     */
    public function getBanner()
    {
        $bannerId = $this->getBannerId();
        try {
            if ($bannerId) {
                $storeId = $this->_storeManager->getStore()->getId();
                /** @var \Ecommage\BannerManager\Model\Banner $block */
                $banner = $this->_bannerFactory->create();
                $banner->setVisibilityFilter(true);
                $banner->setStoreId($storeId)->load($bannerId);
                if ($banner->isActive()) {
                    return $banner;
                }
            }
        } catch (\Exception $e) {
            $this->_logger->debug($e->getMessage());
        }

        return null;
    }

    /**
     * @param Item $bannerItem
     *
     * @return string|null
     */
    public function getImageDesktopUrl($bannerItem)
    {
        try {
            return $bannerItem->getImageUrl(Item::IMAGE_DESKTOP);
        } catch (\Exception $e) {
            $this->_logger->log($e->getMessage());
        }

        return null;
    }

    /**
     * @param Item $bannerItem
     *
     * @return string|null
     */
    public function getImageMobileUrl($bannerItem)
    {
        try {
            return $bannerItem->getImageUrl(Item::IMAGE_MOBILE);
        } catch (\Exception $e) {
            $this->_logger->log($e->getMessage());
        }

        return null;
    }

    /**
     * @param Item $bannerItem
     *
     * @return string|null
     */
    public function getImageVideoPoster($bannerItem)
    {
        try {
            return $bannerItem->getImageDesktop($bannerItem);
        } catch (\Exception $e) {
            $this->_logger->log($e->getMessage());
        }

        return null;
    }

    /**
     * @param $bannerItem
     *
     * @return bool
     */
    public function isVideo($bannerItem)
    {
        if ($bannerItem->getData(Item::MEDIA_TYPE) == MediaType::MEDIA_TYPE_VIDEO) {
            return true;
        }

        return false;
    }

    /**
     * @return array
     */
    public function getBannerItems()
    {
        $timeNow = strtotime((new \DateTime())->format(\Magento\Framework\Stdlib\DateTime::DATETIME_PHP_FORMAT));
        $items  = [];
        $banner = $this->getBanner();
        if ($banner && $banner->getBannerItems()) {
            foreach ($banner->getBannerItems() as $item) {
                if($item['start_date'] && $item['end_date']){
                    if(strtotime($item['start_date']) <= $timeNow && strtotime($item['end_date']) >= $timeNow){
                        $items[] = $this->convertBannerItem($item);
                    }
                }
                else if($item['start_date'] && !$item['end_date']){
                    if(strtotime($item['start_date']) <= $timeNow){
                        $items[] = $this->convertBannerItem($item);
                    }
                }
                else if($item['end_date'] && !$item['start_date']){
                    if(strtotime($item['end_date']) >= $timeNow){
                        $items[] = $this->convertBannerItem($item);
                    }
                }else{
                    $items[] = $this->convertBannerItem($item);
                }
            }
        }

        return $items;
    }

    /**
     * @param $data
     *
     * @return Item
     */
    protected function convertBannerItem($data)
    {
        if (is_array($data)) {
            /** @var \Ecommage\BannerManager\Model\Banner\Item $model */
            $model = $this->_bannerItemFactory->create();
            $model->setData($data);
            return $model;
        }

        return $data;
    }

    /**
     * @param $link
     *
     * @return string
     */
    public function getLink($link)
    {
        return $this->getBaseUrl() . $link;
    }

    /**
     * @return array|null
     */
    public function getCacheKeyInfo()
    {
        $cacheInfo = parent::getCacheKeyInfo();
        return array_merge($cacheInfo, $this->getData());
    }

    /**
     * Return identifiers for produced content
     *
     * @return array
     */
    public function getIdentities()
    {
        $identities = [];
        if ($banner = $this->getBanner()) {
            $identities = $banner->getIdentities();
        }

        $items = $this->getBannerItems();
        /** @var Item $item */
        foreach ($items as $item) {
            $identities = array_merge($identities, $item->getIdentities());
        }

        return $identities;
    }

    /**
     * @return string
     */
    public function getElementId(): string
    {
        $cacheKey = $this->getCacheKey();
        return str_replace(static::CACHE_KEY_PREFIX,'banner-', $cacheKey);
    }

    /**
     * @param $item
     * @return array|string|string[]|null
     */
    public function getVideoYoutube($item)
    {
        $videoLink = $item->getVideoLink();
        if ($videoLink) {
            if (strpos($videoLink, 'youtube.com') !== false) {
                return preg_replace("/\s*[a-zA-Z\/\/:\.]*youtube.com\/watch\?v=([a-zA-Z0-9\-_]+)([a-zA-Z0-9\/\*\-\_\?\&\;\%\=\.]*)/i", "<div class='embed-youtube' data-video-id='$1'><div class='embed-youtube-play'></div></div>", $videoLink);
            }
        }
        return false;
    }
}
