<?php

namespace Ecommage\BannerManager\Controller\Adminhtml\Item;

/**
 * Class NewAction
 */
class NewAction extends BannerItem
{
    public function execute()
    {
        $this->_forward('edit');
    }
}
