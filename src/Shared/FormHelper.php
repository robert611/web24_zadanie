<?php

declare(strict_types=1);

namespace App\Shared;

use Symfony\Component\Validator\ConstraintViolationListInterface;

class FormHelper
{
    public static function mapValidationErrorsToPlainString(ConstraintViolationListInterface $errorsList): string
    {
        $result = '';

        foreach ($errorsList as $error) {
            $result .= $error->getMessage() . " ";
        }

        return trim($result);
    }
}
