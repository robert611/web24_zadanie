<?php

declare(strict_types=1);

namespace App\Tests\Unit\Shared;

use App\Shared\ValidationHelper;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Validator\ConstraintViolation;
use Symfony\Component\Validator\ConstraintViolationList;

class ValidationHelperTest extends TestCase
{
    public function testMapValidationErrorsToPlainString(): void
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
        $result = ValidationHelper::mapValidationErrorsToPlainString($violationList);

        // then
        self::assertEquals('Błąd 1. Błąd 2.', $result);
    }

    public function testMapValidationErrorsToPlainArray(): void
    {
        // given
        $violationList = new ConstraintViolationList();
        $violationList->add(new ConstraintViolation(
            message: 'Błąd 1.',
            messageTemplate: null,
            parameters: [],
            root: null,
            propertyPath: 'name',
            invalidValue: null,
        ));
        $violationList->add(new ConstraintViolation(
            'Błąd 2.',
            messageTemplate: null,
            parameters: [],
            root: null,
            propertyPath: 'email',
            invalidValue: null,
        ));

        // when
        $result = ValidationHelper::mapValidationErrorsToPlainArray($violationList);

        // then
        self::assertEquals(['field' => 'name', 'message' => 'Błąd 1.'], $result[0]);
        self::assertEquals(['field' => 'email', 'message' => 'Błąd 2.'], $result[1]);
    }
}
