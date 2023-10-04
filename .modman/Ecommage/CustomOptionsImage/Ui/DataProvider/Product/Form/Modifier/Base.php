<?php

namespace Ecommage\CustomOptionsImage\Ui\DataProvider\Product\Form\Modifier;

use Magento\Catalog\Ui\DataProvider\Product\Form\Modifier\AbstractModifier;
use Magento\Catalog\Ui\DataProvider\Product\Form\Modifier\CustomOptions;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Ui\Component\Form\Element\DataType\Text;
use Magento\Ui\Component\Form\Element\Input;
use Magento\Ui\Component\Form\Element\Textarea;
use Magento\Ui\Component\Form\Field;

class Base extends AbstractModifier
{
    /**
     * @var StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * @param StoreManagerInterface $storeManager
     */
    public function __construct(
        StoreManagerInterface $storeManager
    ) {
        $this->_storeManager = $storeManager;
    }

    /**
     * @var array
     */
    protected $meta = [];

    /**
     * {@inheritdoc}
     */
    public function modifyData(array $data)
    {
        return $data;
    }

    /**
     * {@inheritdoc}
     */
    public function modifyMeta(array $meta)
    {
        $this->meta = $meta;

        $this->addFields();

        return $this->meta;
    }

    /**
     * Adds fields to the meta-data
     */
    protected function addFields()
    {
        $groupCustomOptionsName    = CustomOptions::GROUP_CUSTOM_OPTIONS_NAME;
        $optionContainerName       = CustomOptions::CONTAINER_OPTION;
        $commonOptionContainerName = CustomOptions::CONTAINER_COMMON_NAME;

        // Add fields to the option
        $this->meta[$groupCustomOptionsName]['children']['options']['children']['record']['children']
        [$optionContainerName]['children'][$commonOptionContainerName]['children']
            = array_replace_recursive(
            $this->meta[$groupCustomOptionsName]['children']['options']['children']['record']['children']
            [$optionContainerName]['children'][$commonOptionContainerName]['children'],
            $this->getOptionFieldsConfig()
        );

        // Add fields to the values
        $this->meta[$groupCustomOptionsName]['children']['options']['children']['record']['children']
        [$optionContainerName]['children']['values']['children']['record']['children']
            = array_replace_recursive(
            $this->meta[$groupCustomOptionsName]['children']['options']['children']['record']['children']
            [$optionContainerName]['children']['values']['children']['record']['children'],
            $this->getValueFieldsConfig()
        );
    }

    /**
     * The custom option fields config
     *
     * @return array
     */
    protected function getOptionFieldsConfig()
    {
        $fields['option_title']       = $this->getOptionTitleFieldConfig();
        $fields['option_description'] = $this->getOptionDescriptionFieldConfig();

        return $fields;
    }

    /**
     * The custom option fields config
     *
     * @return array
     */
    protected function getValueFieldsConfig()
    {
        $fields['description'] = $this->getDescriptionFieldConfig();
        $fields['image']       = $this->getImageFieldConfig();
        $fields['uploader']    = $this->getUploadFieldConfig();

        return $fields;
    }

    /**
     * Get option description field config
     *
     * @return array
     */
    protected function getOptionDescriptionFieldConfig()
    {
        return [
            'arguments' => [
                'data' => [
                    'config' => [
                        'label'         => __('Option Description'),
                        'componentType' => Field::NAME,
                        'formElement'   => Textarea::NAME,
                        'dataScope'     => 'option_description',
                        'dataType'      => Text::NAME,
                        'sortOrder'     => 65,
                    ],
                ],
            ],
        ];
    }

    /**
     * Get option title field config
     *
     * @return array
     */
    protected function getOptionTitleFieldConfig()
    {
        return [
            'arguments' => [
                'data' => [
                    'config' => [
                        'label'         => __('Option Title'),
                        'componentType' => Field::NAME,
                        'formElement'   => Textarea::NAME,
                        'dataScope'     => 'option_title',
                        'dataType'      => Text::NAME,
                        'sortOrder'     => 64,
                    ],
                ],
            ],
        ];
    }

    /**
     * Get description field config
     *
     * @return array
     */
    protected function getDescriptionFieldConfig()
    {
        return [
            'arguments' => [
                'data' => [
                    'config' => [
                        'label'         => __('Description'),
                        'componentType' => Field::NAME,
                        'formElement'   => Textarea::NAME,
                        'dataType'      => Text::NAME,
                        'dataScope'     => 'description',
                        'sortOrder'     => 43
                    ],
                ],
            ],
        ];
    }

    /**
     * Get image field config
     *
     * @return array
     */
    protected function getImageFieldConfig()
    {
        return [
            'arguments' => [
                'data' => [
                    'config' => [
                        'label'         => __('Image'),
                        'componentType' => Field::NAME,
                        'component'     => 'Ecommage_CustomOptionsImage/js/form/element/preview-option-image',
                        'elementTmpl'   => 'Ecommage_CustomOptionsImage/form/element/preview-option-image',
                        'formElement'   => Input::NAME,
                        'dataType'      => Text::NAME,
                        'dataScope'     => 'image',
                        'sortOrder'     => 41,
                        'imports'       => [
                            'base_url' => $this->_storeManager->getStore()->getBaseUrl(),
                        ]
                    ],
                ],
            ],
        ];
    }

    /**
     * Get image field config
     *
     * @return array
     */
    protected function getUploadFieldConfig()
    {
        return [
            'arguments' => [
                'data' => [
                    'config' => [
                        'componentType'  => Field::NAME,
                        'component'      => 'Ecommage_CustomOptionsImage/js/form/element/file-upload',
                        'template'       => 'Ecommage_CustomOptionsImage/form/element/uploader',
                        'formElement'    => 'fileUploader',
                        'dataType'       => Text::NAME,
                        'dataScope'      => 'uploader',
                        'sortOrder'      => 42,
                        'uploaderConfig' => [
                            'url' => 'option/option/upload'
                        ],
                    ],
                ],
            ],
        ];
    }
}
