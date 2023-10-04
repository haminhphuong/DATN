<?php
/**
 * Data
 *
 * @copyright Copyright © 2021 Ecommage. All rights reserved.
 * @author    phuonghm@ecommage.com
 */

namespace Ecommage\CustomFormProduct\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Amasty\ShopbyBase\Model\ResourceModel\OptionSetting\CollectionFactory;

class Data extends AbstractHelper
{
    /**
     * @var CollectionFactory
     */
    protected $optionSettingFactory;

    public function __construct
    (
        Context               $context,
        CollectionFactory $optionSettingFactory
    )
    {
        $this->optionSettingFactory = $optionSettingFactory;
        parent::__construct($context);
    }

    public function getUserManualAndInsuranceBrand($brandId = null)
    {
        $data = $this->optionSettingFactory->create()
            ->addFieldToFilter('value',$brandId)
            ->getFirstItem();

        return $data;
    }
}
