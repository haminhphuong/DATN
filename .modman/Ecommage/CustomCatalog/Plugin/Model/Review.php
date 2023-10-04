<?php

namespace Ecommage\CustomCatalog\Plugin\Model;

class Review
{
    /**
     * Validate review summary fields
     *
     * @return bool|string[]
     */
    public function aroundValidate(\Magento\Review\Model\Review $subject)
    {
        $errors = [];

        if (!\Zend_Validate::is($subject->getNickname(), 'NotEmpty')) {
            $errors[] = __('Please enter a nickname.');
        }

        if (!\Zend_Validate::is($subject->getDetail(), 'NotEmpty')) {
            $errors[] = __('Please enter a review.');
        }

        if (empty($errors)) {
            return true;
        }
        return $errors;
    }
}
