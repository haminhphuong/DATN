<?php

namespace Ecommage\SliderCollection\Controller\Adminhtml\Slider;

/**
 * Class NewAction
 *
 * @package Ecommage\SliderCollection\Controller\Adminhtml\Customer\Slider
 */
class NewAction extends \Magento\Backend\App\Action
{
    public function execute()
    {
        $this->_forward('edit');
    }

    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Ecommage_SliderCollection::slider_collection');
    }
}
