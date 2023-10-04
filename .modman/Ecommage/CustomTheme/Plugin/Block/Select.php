<?php

namespace Ecommage\CustomTheme\Plugin\Block;

/**
 * GTranslator Select
 */
class Select
{
    /**
     * Check mobile device or not
     * @return boolean
     */
    public function aroundIsMobileDevice(\Zealousweb\GTranslator\Block\Select $subject, \Closure $proceed)
    {
        $aMobileUA = array(
            '/iphone/i' => 'iPhone',
            '/ipod/i' => 'iPod',
            '/ipad/i' => 'iPad',
            '/android/i' => 'Android',
            '/blackberry/i' => 'BlackBerry',
            '/webos/i' => 'Mobile'
        );
        
        //Return true if Mobile User Agent is detected
        if(isset($_SERVER['HTTP_USER_AGENT'])){
            foreach($aMobileUA as $sMobileKey => $sMobileOS){
                if(preg_match($sMobileKey, $_SERVER['HTTP_USER_AGENT'])){
                    return true;
                }
            }
        }
        //Otherwise return false..
        return false;
    }
}
