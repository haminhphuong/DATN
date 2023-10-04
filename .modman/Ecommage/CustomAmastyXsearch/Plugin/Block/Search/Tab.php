<?php

namespace Ecommage\CustomAmastyXsearch\Plugin\Block\Search;

use Magento\Framework\View\Element\Template;

class Tab extends \Amasty\Xsearch\Block\Search\Tab
{
    /**
     * Tab constructor.
     *
     * @param Template\Context                  $context
     * @param \Magento\Framework\Module\Manager $moduleManager
     * @param \Amasty\Xsearch\Helper\Data       $helper
     * @param array                             $data
     */
    public function __construct(
        Template\Context $context, \Magento\Framework\Module\Manager $moduleManager,
        \Amasty\Xsearch\Helper\Data $helper, array $data = []
    ) {
        parent::__construct($context, $moduleManager, $helper, $data);
    }

    /**
     * @param \Amasty\Xsearch\Block\Search\Tab $subject
     * @param                                  $result
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     * @return mixed
     */
    public function afterGetTabs(\Amasty\Xsearch\Block\Search\Tab $subject, $result)
    {
        foreach ($result as $key => $value) {
            if (in_array('amsearch-blog-tab', $value)) {
                unset($result[$key]);
            }
        }

        return $result;
    }
}
