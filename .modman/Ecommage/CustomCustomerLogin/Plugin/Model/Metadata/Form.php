<?php

namespace Ecommage\CustomCustomerLogin\Plugin\Model\Metadata;

use Magento\Customer\Api\AddressMetadataInterface;

class Form
{
    protected $form;

    public function __construct
    (
        AddressMetadataInterface $addressMetadataService
    )
    {
        $this->_addressMetadataService = $addressMetadataService;
    }


    public function afterGetAllowedAttributes(\Magento\Customer\Model\Metadata\Form $subject, $result)
    {
        $attribute = $this->_addressMetadataService->getAttributes('customer_address_edit');
        if ($attribute && array_key_exists('lastname',$attribute)){
            $att = [
                'lastname' => $attribute['lastname']
            ];
            $result =  array_merge($result,$att);
        }
        
        return $result;
    }
    
}