<?php
namespace Ecommage\CustomSales\Model\Order\Email\Container;

use Magento\Sales\Model\Order\Email\Container\Container;
use Magento\Sales\Model\Order\Email\Container\IdentityInterface;

class CancelOrderIdentity extends Container implements IdentityInterface
{
    /**
     * Configuration paths
     */
    const XML_PATH_EMAIL_COPY_METHOD = 'sales_email/cancel_order/copy_method';
    const XML_PATH_EMAIL_COPY_TO = 'sales_email/cancel_order/copy_to';
    const XML_PATH_EMAIL_GUEST_TEMPLATE = 'sales_email/cancel_order/guest_template';
    const XML_PATH_EMAIL_TEMPLATE = 'sales_email/cancel_order/template';
    const XML_PATH_EMAIL_IDENTITY = 'sales_email/cancel_order/identity';
    const XML_PATH_EMAIL_ENABLED = 'sales_email/cancel_order/enabled';

    /**
     * Is email enabled
     *
     * @return bool
     */
    public function isEnabled()
    {
        return $this->scopeConfig->isSetFlag(
            self::XML_PATH_EMAIL_ENABLED,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $this->getStore()->getStoreId()
        );
    }

    /**
     * Return email copy_to list
     *
     * @return array|bool
     */
    public function getEmailCopyTo()
    {
        $data = $this->getConfigValue(self::XML_PATH_EMAIL_COPY_TO, $this->getStore()->getStoreId());
        if (!empty($data)) {
            return array_map('trim', explode(',', $data));
        }
        return false;
    }

    /**
     * Return email copy method
     *
     * @return mixed
     */
    public function getCopyMethod()
    {
        return $this->getConfigValue(self::XML_PATH_EMAIL_COPY_METHOD, $this->getStore()->getStoreId());
    }

    /**
     * Return guest template id
     *
     * @return mixed
     */
    public function getGuestTemplateId()
    {
        return $this->getConfigValue(self::XML_PATH_EMAIL_GUEST_TEMPLATE, $this->getStore()->getStoreId());
    }

    /**
     * Return template id
     *
     * @return mixed
     */
    public function getTemplateId()
    {
        return $this->getConfigValue(self::XML_PATH_EMAIL_TEMPLATE, $this->getStore()->getStoreId());
    }

    /**
     * Return email identity
     *
     * @return mixed
     */
    public function getEmailIdentity()
    {
        return $this->getConfigValue(self::XML_PATH_EMAIL_IDENTITY, $this->getStore()->getStoreId());
    }
}
