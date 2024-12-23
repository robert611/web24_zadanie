<?php

declare(strict_types=1);

namespace App\Tests\Unit\Shared;

use App\Shared\FormHelper;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Validator\ConstraintViolation;
use Symfony\Component\Validator\ConstraintViolationList;

class FormHelperTest extends TestCase
{
    public function testMapValidationErrorsToPlainArray(): void
    {
        // given
        $violationList = new ConstraintViolationList();
        $violationList->add(new ConstraintViolation(
            message: 'Błąd 1.',
            messageTemplate: null,
            parameters: [],
            root: null,
            propertyPath: null,
            invalidValue: null,
        ));
        $violationList->add(new ConstraintViolation(
            'Błąd 2.',
            messageTemplate: null,
            parameters: [],
            root: null,
            propertyPath: null,
            invalidValue: null,
        ));

        // when
        $result = FormHelper::mapValidationErrorsToPlainString($violationList);

        // then
        self::assertEquals('Błąd 1. Błąd 2.', $result);
    }
}
