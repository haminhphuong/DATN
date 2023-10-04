<?php

namespace Ecommage\CustomJet\Plugin;

use Laminas\Escaper\Escaper;

class EscaperPlugin
{

    /**
     * @param Escaper $subject
     * @param string  $string
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     * @return array
     */
    public function beforeEscapeUrl(Escaper $subject, $string)
    {
        $string = (string)$string;
        return [$string];
    }
}
