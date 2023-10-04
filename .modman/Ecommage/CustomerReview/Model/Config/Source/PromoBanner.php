<?php

namespace Ecommage\CustomerReview\Model\Config\Source;

use Magento\Eav\Model\Entity\Attribute\Source\AbstractSource;
use Amasty\PromoBanners\Model\ResourceModel\Rule\CollectionFactory;

class PromoBanner extends AbstractSource
{
    const ACTIVE = 1;
    /**
     * @var CollectionFactory
     */
    protected $ruleCollectionFactory;

    /**
     * PromoBanner constructor.
     *
     * @param CollectionFactory $ruleCollectionFactory
     */
    public function __construct
    (
        CollectionFactory $ruleCollectionFactory
    )
    {
        $this->ruleCollectionFactory = $ruleCollectionFactory;
    }

    /**
     * Options getter
     *
     * @return array
     */
    public function getAllOptions(): ?array
    {
        if (!$this->_options) {
            $banners = $this->ruleCollectionFactory->create()
                                                   ->addFieldToFilter('is_active', self::ACTIVE);
            foreach ($banners as $banner){
                $this->_options = [
                    ['label' => __($banner->getRuleName()), 'value' => $banner->getId()],
                ];
            }
        }
        return $this->_options;
    }
}
