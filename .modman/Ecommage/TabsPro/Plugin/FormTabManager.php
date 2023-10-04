<?php

namespace Ecommage\TabsPro\Plugin;

use Magezon\TabsPro\Block\Adminhtml\Form\Renderer\TabManager;

/**
 * Class FormTabManager
 */
class FormTabManager
{
    /**
     * @param TabManager $subject
     * @param            $result
     *
     * @return array
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function afterGetFields(TabManager $subject, $result)
    {
        $result['maincontent_items_per_column_mb'] = ['default' => 1];
        return $result;
    }

    /**
     * @param TabManager $subject
     * @param            $result
     *
     * @return string
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function afterGetTemplate(TabManager $subject, $result)
    {
        return 'Ecommage_TabsPro::form/field/array.phtml';
    }
}
