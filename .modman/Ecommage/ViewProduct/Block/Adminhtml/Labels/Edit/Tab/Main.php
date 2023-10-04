<?php

namespace Ecommage\ViewProduct\Block\Adminhtml\Labels\Edit\Tab;

class Main extends \Amasty\Label\Block\Adminhtml\Labels\Edit\Tab\Main
{
    /**
     * Prepare form before rendering HTML
     *
     * @return Main|\Magento\Backend\Block\Widget\Form\Generic
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @SuppressWarnings(PHPMD.NPathComplexity)
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    protected function _prepareForm()
    {
        $model = $this->_coreRegistry->registry('current_amasty_label');
        /** @var \Magento\Framework\Data\Form $form */
        $form = $this->_formFactory->create();
        $form->setHtmlIdPrefix('label_');

        $fieldset = $form->addFieldset('general', ['legend' => __('Label Information')]);
        if ($model->getLabelId()) {
            $fieldset->addField('label_id', 'hidden', ['name' => 'label_id']);
        }
        $fieldset->addField('open_tab_input',
                            'hidden',
                            [
                                'name' => 'open_tab_input',
                                'after_element_html' => '<script>
                    require([
                      "jquery",
                      "Amasty_Label/js/amlabel"
                    ], function ($) {
                       $("body").amLabeltabs();
                    });
                 </script>'
                            ]
        );

        $fieldset->addField('name', 'text', array(
            'name' => 'name',
            'label' => __('Name'),
            'title' => __('Name'),
            'required' => true
        ));

        $fieldset->addField('status', 'select', array(
            'name' => 'status',
            'label' => __('Status'),
            'title' => __('Status'),
            'values'    => array(
                0 => __('Inactive'),
                1 => __('Active'),
            )
        ));

        $validateClass = sprintf(
            'validate-not-negative-number validate-length maximum-length-%d',
            5
        );
        $fieldset->addField('pos', 'text', array(
            'label'     => __('Priority'),
            'name'      => 'pos',
            'note'      => __('Use 0 to show label first, and 99 to show it last'),
            'class' => $validateClass
        ));

        $fieldset->addField('is_single', 'select', array(
            'label'     => __('Hide if Label with Higher Priority is Already Applied'),
            'name'      => 'is_single',
            'values'    => array(
                0 => __('No'),
                1 => __('Yes'),
            ),
        ));

        $fieldset->addField('use_for_parent', 'select', array(
            'label'     => __('Use for Parent'),
            'title'     => __('Use for Parent'),
            'name'      => 'use_for_parent',
            'note'      => __('Display child`s label for parent (configurable and grouped products only)'),
            'values'   => array(
                '0' => __('No'),
                '1' => __('Yes'),
            ),
        ));

        $fieldset->addField('is_hot_sale', 'select', array(
            'name' => 'is_hot_sale',
            'label' => __('Is Hot Sale'),
            'title' => __('Is Hot Sale'),
            'values'    => array(
                0 => __('No'),
                1 => __('Yes'),
            )
        ));

        if (!$this->_storeManager->isSingleStoreMode()) {
            $field = $fieldset->addField(
                'stores',
                'multiselect',
                [
                    'label' => __('Store'),
                    'title' => __('Store'),
                    'values' => $this->_systemStore->getStoreValuesForForm(),
                    'name' => 'stores',
                    'required' => true
                ]
            );
            $renderer = $this->getLayout()->createBlock(
                'Magento\Backend\Block\Store\Switcher\Form\Renderer\Fieldset\Element'
            );
            $field->setRenderer($renderer);
        } else {
            $fieldset->addField(
                'stores',
                'hidden',
                ['name' => 'stores', 'value' => $this->_storeManager->getStore(true)->getId()]
            );
        }
        if(!$model->getData()) {
            $model->setData('status', 1);
        }
        $form->setValues($model->getData());
        $this->setForm($form);
        return \Magento\Backend\Block\Widget\Form\Generic::_prepareForm();
    }
}
