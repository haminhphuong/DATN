<?php
namespace Ecommage\CustomCustomerLogin\Helper;

use Magento\Framework\App\Helper\AbstractHelper;

class Data extends AbstractHelper
{
    /**
     * @param $street
     * @return mixed|string
     */
    public function getStreetAddress($street){
        if (is_array($street)) {
            $street = implode(', ', $street);
        }
        return $street;
    }
}
