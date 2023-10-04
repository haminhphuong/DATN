<?php
/**
 * Product attribute edit form observer
 *
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Ecommage\CustomCatalog\Observer\Edit\Tab\Front;

use Magento\Config\Model\Config\Source;
use Magento\Framework\Module\Manager;
use Magento\Framework\Event\ObserverInterface;

/**
 * Observer for Product Attribute Form
 */
class ProductAttributeFormBuildFrontTabObserver implements ObserverInterface
{
    /**
     * @var \Magento\Config\Model\Config\Source\Yesno
     */
    protected $optionList;

    /**
     * @var \Magento\Framework\Module\Manager
     */
    protected $moduleManager;

    /**
     * @param Manager $moduleManager
     * @param Source\Yesno $optionList
     */
    public function __construct(Manager $moduleManager, Source\Yesno $optionList)
    {
        $this->optionList = $optionList;
        $this->moduleManager = $moduleManager;
    }

    /**
     * Execute
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @return void
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        if (!$this->moduleManager->isOutputEnabled('Magento_LayeredNavigation')) {
            return;
        }

        /** @var \Magento\Framework\Data\Form\AbstractForm $form */
        $form = $observer->getForm();

        $fieldset = $form->getElement('front_fieldset');
        $yesno = $this->optionList->toOptionArray();

        $fieldset->addField(
            'position_compare',
            'text',
            [
                'name' => 'position_compare',
                'label' => __('Position Compare'),
                'title' => __('Position Compare'),
                'note' => __('Position of attribute in compare page.'),
                'class' => 'validate-digits'
            ],
            'is_comparable'
        );
        $fieldset->addField(
            'visual_swatch_in_filter',
            'select',
            [
                'name' => 'visual_swatch_in_filter',
                'label' => __('Use Visual Swatch In Filter'),
                'title' => __('Use Visual Swatch In Filter'),
                'values' => $yesno
            ]
        );
    }
}
