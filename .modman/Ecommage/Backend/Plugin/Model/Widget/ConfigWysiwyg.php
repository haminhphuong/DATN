<?php

namespace Ecommage\Backend\Plugin\Model\Widget;

use Magento\Framework\DataObject;
use Magento\Framework\View\Asset\Repository;

/**
 * Class ConfigWysiwyg
 *
 * @package Ecommage\Backend\Plugin\Model\Widget
 */
class ConfigWysiwyg
{
    /**
     * @var Repository
     */
    protected $_assetRepo;

    /**
     * ConfigWysiwyg constructor.
     */
    public function __construct(
        Repository $assetRepo
    ) {
        $this->_assetRepo = $assetRepo;
    }

    /**
     * Return url to wysiwyg plugin
     *
     * @return string
     */
    public function getWysiwygJsPluginLinkSrc()
    {
        return $this->_assetRepo->getUrl('Ecommage_Backend/js/tiny_mce_4/plugins/link/plugin.min.js');
    }

    /**
     * @param                               $subject
     * @param DataObject $config
     *
     * @return array
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function afterGetPluginSettings($subject, $config)
    {
        $config['plugins'][] = [
            'name' => 'link',
            'src'  => $this->getWysiwygJsPluginLinkSrc()
        ];

        return $config;
    }
}
