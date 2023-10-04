<?php

namespace Ecommage\CustomTheme\Plugin;

use Amasty\LazyLoad\Model\Output\LazyLoadProcessor;

class ProcessLazyImages
{
    /**
     * @param LazyLoadProcessor $subject
     * @param callable $proceed
     * @param $output
     * @return void
     * @SuppressWarnings(PHPMD)
     */
    public function aroundProcessLazyImages(LazyLoadProcessor $subject, callable $proceed, &$output): void
    {
        $tempOutput = preg_replace('/<script[^>]*>(?>.*?<\/script>)/is', '', $output);
        if (!$tempOutput) {
            return;
        }
        $proceed($output);
    }
}
