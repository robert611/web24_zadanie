<?php

declare(strict_types=1);

namespace App\Shared;

use Symfony\Component\Validator\ConstraintViolationListInterface;

class ValidationHelper
{
    public static function mapValidationErrorsToPlainString(ConstraintViolationListInterface $errorsList): string
    {
        $result = '';

        foreach ($errorsList as $error) {
            $result .= $error->getMessage() . " ";
        }

        return trim($result);
    }

    /**
     * @return array<int, array{field: string, message: string}>
     */
    public static function mapValidationErrorsToPlainArray(ConstraintViolationListInterface $errorsList): array
    {
        $result = [];

        foreach ($errorsList as $error) {
            $result[] = [
                'field' => $error->getPropertyPath(),
                'message' => $error->getMessage(),
            ];
        }

        return $result;
    }
}
