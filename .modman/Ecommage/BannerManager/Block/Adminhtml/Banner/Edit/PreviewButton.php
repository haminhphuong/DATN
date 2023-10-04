<?php

namespace Ecommage\BannerManager\Block\Adminhtml\Banner\Edit;

use Magento\Framework\View\Element\UiComponent\Control\ButtonProviderInterface;

/**
 * Class ResetButton
 */
class PreviewButton extends GenericButton implements ButtonProviderInterface
{
    public function getButtonData()
    {
        return [
            'label'      => __('Save And Preview'),
            'class'          => 'save',
            'data_attribute' => [
                'mage-init' => [
                    'button' => ['event' => 'saveAndContinueEdit'],
                ],
            ],
            'sort_order' => 30
        ];
    }
}
