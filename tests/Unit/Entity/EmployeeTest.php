<?php

declare(strict_types=1);

namespace App\Tests\Unit\Entity;

use App\Entity\Company;
use App\Entity\Employee;
use DateTimeImmutable;
use PHPUnit\Framework\TestCase;

final class EmployeeTest extends TestCase
{
    /**
     * @test
     */
    public function canCreateNewEntity(): void
    {
        // given
        $company = new Company();

        // when
        $employee = Employee::create(
            $company,
            'John',
            'Doe',
            'john.do@example.com',
            '989765333',
        );

        // then
        self::assertEquals($company, $employee->getCompany());
        self::assertEquals('John', $employee->getFirstName());
        self::assertEquals('Doe', $employee->getLastName());
        self::assertEquals('john.do@example.com', $employee->getEmail());
        self::assertEquals('989765333', $employee->getPhoneNumber());
        self::assertTrue($employee->getCreatedAt() < new DateTimeImmutable());
        self::assertTrue($employee->getUpdatedAt() < new DateTimeImmutable());
    }

    /**
     * @test
     */
    public function canUpdate(): void
    {
        // given
        $company = new Company();

        // and given
        $employee = Employee::create(
            $company,
            'John',
            'Doe',
            'john.doe@example.com',
            '+48 989 765 333',
        );

        // when
        $employee->update(
            "Laura",
            "Bennet",
            "laura.benntet@example.com",
            "+48 232 345 234",
        );

        // then
        self::assertEquals($company, $employee->getCompany());
        self::assertEquals('Laura', $employee->getFirstName());
        self::assertEquals('Bennet', $employee->getLastName());
        self::assertEquals('laura.benntet@example.com', $employee->getEmail());
        self::assertEquals('+48 232 345 234', $employee->getPhoneNumber());
    }
}
