<?php

namespace Ecommage\BannerManager\Block\Adminhtml\Banner\Edit;

use Magento\Framework\View\Element\UiComponent\Control\ButtonProviderInterface;

/**
 * Class SaveButton
 */
class SaveButton extends GenericButton implements ButtonProviderInterface
{
    public function getButtonData()
    {
        $result = [];
        if ($this->getId()) {
            $result = [
                'label'          => __('Save'),
                'class'          => 'save primary',
                'data_attribute' => [
                    'mage-init' => ['button' => ['event' => 'save']],
                    'form-role' => 'save',
                ],
                'sort_order'     => 90,
            ];
        }

        return $result;
    }
}
