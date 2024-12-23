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
        $company = new Company();

        $employee = Employee::create(
            $company,
            'John',
            'Doe',
            'john.do@example.com',
            '989765333',
        );

        self::assertEquals($company, $employee->getCompany());
        self::assertEquals('John', $employee->getFirstName());
        self::assertEquals('Doe', $employee->getLastName());
        self::assertEquals('john.do@example.com', $employee->getEmail());
        self::assertEquals('989765333', $employee->getPhoneNumber());
        self::assertTrue($employee->getCreatedAt() < new DateTimeImmutable());
        self::assertTrue($employee->getUpdatedAt() < new DateTimeImmutable());

    }
}
