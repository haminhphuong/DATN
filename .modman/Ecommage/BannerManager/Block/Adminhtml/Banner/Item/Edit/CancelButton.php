<?php
namespace Ecommage\BannerManager\Block\Adminhtml\Banner\Item\Edit;
use Magento\Framework\View\Element\UiComponent\Control\ButtonProviderInterface;

/**
 * Class BackButton
 */
class CancelButton extends GenericButton implements ButtonProviderInterface
{
    /**
     * @inheritdoc
     *
     * @return array
     */
    public function getButtonData()
    {
        return [
            'label' => __('Cancel'),
            'on_click' => '',
            'data_attribute' => [
                'mage-init' => [
                    'Magento_Ui/js/form/button-adapter' => [
                        'actions' => [
                            [
                                'targetName' => 'banner_form.areas.item.item.banner_items_update_modal',
                                'actionName' => 'closeModal'
                            ],
                        ],
                    ],
                ],
            ],
            'sort_order' => 20
        ];
    }
}
