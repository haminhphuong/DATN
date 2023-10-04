<?php

namespace Ecommage\TabsPro\Helper;

use Amasty\Amp\Model\Detection\MobileDetect;
use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;

class Data extends AbstractHelper
{
    /**
     * @var MobileDetect
     */
    private $mobileDetect;

    /**
     * Data constructor.
     *
     * @param MobileDetect $mobileDetect
     * @param Context      $context
     */
    public function __construct(
        MobileDetect $mobileDetect,
        Context $context
    ) {
        $this->mobileDetect = $mobileDetect;
        parent::__construct($context);
    }

    /**
     * @return bool
     */
    public function isMobile()
    {
        return $this->mobileDetect->isMobile();
    }
}
