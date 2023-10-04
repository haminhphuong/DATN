<?php

namespace Ecommage\CustomCatalog\Observer\Admin;

use Amasty\ShopbyBase\Model\OptionSetting;
use Magento\Cms\Model\Wysiwyg\Config;
use Magento\Framework\Data\Form;
use Magento\Framework\Event\ObserverInterface;

class AddFieldOtherToForm implements ObserverInterface
{
    /**
     * @var Config
     */
    private $wysiwygConfig;
    /**
     * @param \Magento\Framework\Event\Observer $observer
     */

    public function __construct(
        Config $wysiwygConfig
    )
    {
        $this->wysiwygConfig = $wysiwygConfig;
    }

    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        /** @var Form $form */
        $form = $observer->getData('form');

        /** @var OptionSetting $setting */
        $setting = $observer->getData('setting');
        $storeId = $observer->getData('store_id');

        $this->addMetaDataFieldset($form);
    }

    /**
     * @param Form $form
     */
    private function addMetaDataFieldset(\Magento\Framework\Data\Form $form)
    {
        $metaDataFieldset = $form->addFieldset(
            'policy_field_set',
            [
                'legend' => __('Policy'),
                'class'=>'form-inline'
            ]
        );

        $metaDataFieldset->addField(
            'user_manual',
            'editor',
            ['name' => 'user_manual',
                'label' => __('User manual'),
                'title' => __('User manual'),
                'wysiwyg' => true,
                'config' => $this->wysiwygConfig->getConfig(['add_variables' => false]),
            ]
        );

        $metaDataFieldset->addField(
            'insurance',
            'editor',
            [
                'name' => 'insurance',
                'label' => __('Insurance'),
                'title' => __('Insurance'),
                'wysiwyg' => true,
                'config' => $this->wysiwygConfig->getConfig(['add_variables' => false]),
            ]
        );
    }


}
