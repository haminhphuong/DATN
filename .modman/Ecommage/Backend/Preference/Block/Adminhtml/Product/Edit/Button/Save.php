<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Ecommage\Backend\Preference\Block\Adminhtml\Product\Edit\Button;

use Magento\Framework\Registry;
use Magento\Framework\View\Element\UiComponent\Context;
use Magento\Ui\Component\Control\Container;
use Magento\Catalog\Block\Adminhtml\Product\Edit\Button\Generic;
use Magento\ConfigurableProduct\Model\Product\Type\Configurable as ConfigurableType;

/**
 * Class Save
 */
class Save extends \Magento\ConfigurableProduct\Block\Adminhtml\Product\Edit\Button\Save
{
    public function __construct(Context $context, Registry $registry)
    {
        parent::__construct($context, $registry);
    }

    protected function getOptions()
    {
        $options[] = [
            'id_hard' => 'save_and_new',
            'label' => __('Save & New'),
            'data_attribute' => [
                'mage-init' => [
                    'buttonAdapter' => [
                        'actions' => [
                            [
                                'targetName' => $this->getSaveTarget(),
                                'actionName' => $this->getSaveAction(),
                                'params' => [
                                    true,
                                    [
                                        'back' => 'new'
                                    ]
                                ]
                            ]
                        ]
                    ]
                ]
            ],
        ];

        if (!$this->context->getRequestParam('popup') && $this->getProduct()->isDuplicable()) {
            $options[] = [
                'label' => __('Save & Duplicate'),
                'id_hard' => 'save_and_duplicate',
                'data_attribute' => [
                    'mage-init' => [
                        'buttonAdapter' => [
                            'actions' => [
                                [
                                    'targetName' => $this->getSaveTarget(),
                                    'actionName' => $this->getSaveAction(),
                                    'params' => [
                                        true,
                                        [
                                            'back' => 'duplicate'
                                        ]
                                    ]
                                ]
                            ]
                        ]
                    ]
                ],
            ];
        }

        $options[] = [
            'id_hard' => 'save_and_close',
            'label' => __('Save & Close'),
            'data_attribute' => [
                'mage-init' => [
                    'buttonAdapter' => [
                        'actions' => [
                            [
                                'targetName' => $this->getSaveTarget(),
                                'actionName' => $this->getSaveAction(),
                                'params' => [
                                    true
                                ]
                            ]
                        ]
                    ]
                ]
            ],
        ];

        $options[] = [
            'id_hard' => 'schedule',
            'label' => __('Save Schedule'),
            'data_attribute' => [
                'mage-init' => [
                    'buttonAdapter' => [
                        'actions' => [
                            [
                                'targetName' => $this->getSaveTarget(),
                                'actionName' => $this->getSaveAction(),
                                'params' => [
                                    true,
                                    [
                                        'back' => 'schedule'
                                    ]
                                ]
                            ]
                        ]
                    ]
                ]
            ],
        ];
        return $options;
    }
}
