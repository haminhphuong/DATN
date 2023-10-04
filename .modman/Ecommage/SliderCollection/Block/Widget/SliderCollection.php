<?php

namespace Ecommage\SliderCollection\Block\Widget;

use Magento\Framework\View\Element\Template;
use Magento\Widget\Block\BlockInterface;
use Ecommage\SliderCollection\Model\ResourceModel\Slider\CollectionFactory;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\UrlInterface;
use Magento\Cms\Model\Template\FilterProvider;

/**
 * Class Slider
 *
 * @package Ecommage\SliderCollection\Block\Widget
 */
class SliderCollection extends Template implements BlockInterface
{
    /**
     * @var string
     */
        protected $_template = 'Ecommage_SliderCollection::widget/slider.phtml';
    /**
     * @var CollectionFactory
     */
    protected $_collection;
    /**
     * @var StoreManagerInterface
     */
    protected $_storeManager;



    public function __construct(
        Template\Context $context,
        CollectionFactory $collection,
        StoreManagerInterface $storeManager,
        array $data = []
    )
    {
        $this->_collection    = $collection;
        $this->_storeManager  = $storeManager;
        parent::__construct($context, $data);
    }

    /**
     * @return mixed
     */
    public function getCollection()
    {
        return $this->_collection->create()
            ->addFieldToFilter('is_active', true)
            ->addFieldToFilter('store_id', [0, $this->getStoreId()]);
    }

    /**
     * @param $fileName
     *
     * @return string
     */
    public function getImage($fileName)
    {
        if (empty($fileName)) {
            return $this->_assetRepo->getUrl('Ecommage_SliderCollection::images/no-image.png');
        }

        return $this->_storeManager->getStore()->getBaseUrl(UrlInterface::URL_TYPE_MEDIA) . $fileName;
    }

    /**
     * @return int
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getStoreId(){
        return $this->_storeManager->getStore()->getId();
    }
}
