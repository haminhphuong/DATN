<?php

namespace Ecommage\CustomTheme\Plugin;

use Amasty\SocialLogin\Block\Popup;

class PopupPlugin
{
    /**
     * @param Popup $subject
     * @param string $result
     * @param string $name
     * @return string
     * @SuppressWarnings(PHPMD)
     */
    public function afterGetChildHtmlAndReplaceIds(Popup $subject, string $result, string $name): string
    {
        return str_replace('form-validate', $name .'-form-validate', $result);
    }
}
