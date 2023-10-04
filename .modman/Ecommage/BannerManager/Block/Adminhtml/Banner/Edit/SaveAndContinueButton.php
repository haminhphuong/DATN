<?php

namespace Ecommage\BannerManager\Block\Adminhtml\Banner\Edit;

use Magento\Framework\View\Element\UiComponent\Control\ButtonProviderInterface;

/**
 * Class SaveAndContinueButton
 */
class SaveAndContinueButton extends GenericButton implements ButtonProviderInterface
{
    /**
     * @return array
     */
    public function getButtonData()
    {
        $title = __('Save And add item');
        if ($this->getId()) {
            $title = __('Save And Preview');
        }

        return [
            'label'          => $title,
            'class'          => 'save',
            'data_attribute' => [
                'mage-init' => [
                    'button' => ['event' => 'saveAndContinueEdit'],
                ],
            ],
            'sort_order'     => 80,
        ];
    }

}
