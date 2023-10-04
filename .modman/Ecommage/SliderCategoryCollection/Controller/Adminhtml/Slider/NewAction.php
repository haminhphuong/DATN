<?php

namespace Ecommage\SliderCategoryCollection\Controller\Adminhtml\Slider;

/**
 * Class NewAction
 *
 * @package Ecommage\SliderCategoryCollection\Controller\Adminhtml\Customer\Slider
 */
class NewAction extends \Magento\Backend\App\Action
{
    public function execute()
    {
        $this->_forward('edit');
    }

    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Ecommage_SliderCategoryCollection::slider_category_collection');
    }
}
