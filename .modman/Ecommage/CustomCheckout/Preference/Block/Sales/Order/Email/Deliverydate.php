<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_Deliverydate
 */

namespace Ecommage\CustomCheckout\Preference\Block\Sales\Order\Email;

use Magento\Framework\View\Element\Template\Context;
use Amasty\Deliverydate\Model\DeliverydateFactory;

class Deliverydate extends \Amasty\Deliverydate\Block\Sales\Order\Info\Deliverydate
{
    public function __construct(Context $context, \Amasty\Deliverydate\Model\ResourceModel\Deliverydate $deliverydateResourceModel, DeliverydateFactory $deliveryDateFactory, \Amasty\Deliverydate\Helper\Data $deliveryHelper, \Magento\Framework\Stdlib\DateTime\DateTime $date, \Magento\Framework\Registry $coreRegistry, \Magento\Sales\Model\OrderRepository $orderRepository, \Magento\Customer\Model\Session $customerSession, array $data = [])
    {
        parent::__construct($context, $deliverydateResourceModel, $deliveryDateFactory, $deliveryHelper, $date, $coreRegistry, $orderRepository, $customerSession, $data);
    }

    /**
     * Before rendering html, but after trying to load cache.
     * Prepare variables for output
     *
     * @return $this
     *
     */
    protected function _beforeToHtml()
    {
        $fields = $this->getFields();
        if (is_array($fields) && !empty($fields)) {
            $deliveryDate = $this->getDeliveryDate();
            $label = '';
            $list = [];
            foreach ($fields as $field) {
                $value = $deliveryDate->getData($field);

                if (!$value) {
                    continue;
                }

                switch ($field) {
                    case 'date':
                        $label = __('Delivery Date') . ':';
                        break;
                    case 'time':
                        $label = __('Delivery Time Interval') . ':';
                        break;
                    case 'comment':
                        $label = __('Delivery Comments') . ':';
                        break;
                }
                $list[$field] = ['label' => $label, 'value' => $value];
            }
            $this->assign('list', $list);
        }

        return parent::_beforeToHtml();
    }
}
