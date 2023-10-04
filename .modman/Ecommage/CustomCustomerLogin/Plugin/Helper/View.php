<?php
namespace Ecommage\CustomCustomerLogin\Plugin\Helper;

use Magento\Customer\Api\CustomerMetadataInterface;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\Escaper;

class View
{
    /**
     * @var CustomerMetadataInterface
     */
    protected $_customerMetadataService;

    /**
     * @var Escaper
     */
    private $escaper;

    public function __construct
    (
        CustomerMetadataInterface $customerMetadataService,
        Escaper $escaper = null
    )
    {
        $this->_customerMetadataService = $customerMetadataService;
        $this->escaper = $escaper ?? ObjectManager::getInstance()->get(Escaper::class);
    }

    /**
     * @inheritdoc
     */
    public function aroundGetCustomerName(\Magento\Customer\Helper\View $subject, \Closure $proceed, $customerData)
    {
        $name = '';
        $prefixMetadata = $this->_customerMetadataService->getAttributeMetadata('prefix');
        if ($prefixMetadata->isVisible() && $customerData->getPrefix()) {
            $name .= $customerData->getPrefix() . ' ';
        }
        $name .= $customerData->getLastname();

        $middleNameMetadata = $this->_customerMetadataService->getAttributeMetadata('middlename');
        if ($middleNameMetadata->isVisible() && $customerData->getMiddlename()) {
            $name .= ' ' . $customerData->getMiddlename();
        }

        $name .= ' '.$customerData->getFirstname();

        $suffixMetadata = $this->_customerMetadataService->getAttributeMetadata('suffix');
        if ($suffixMetadata->isVisible() && $customerData->getSuffix()) {
            $name .= ' ' . $customerData->getSuffix();
        }

        return $this->escaper->escapeHtml($name);
    }
}
