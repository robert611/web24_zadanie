<?php

declare(strict_types=1);

namespace App\Tests\Unit\Entity;

use App\Entity\Company;
use DateTimeImmutable;
use PHPUnit\Framework\TestCase;

final class CompanyTest extends TestCase
{
    /**
     * @test
     */
    public function canCreateNewEntity(): void
    {
        $company = Company::create(
            "General Dynamics",
            "0224111111",
            "Lazurowa 48/10",
            "Poznań",
            "60-001"
        );

        self::assertEquals("General Dynamics", $company->getName());
        self::assertEquals("0224111111", $company->getNip());
        self::assertEquals("Lazurowa 48/10", $company->getAddress());
        self::assertEquals("Poznań", $company->getCity());
        self::assertEquals("60-001", $company->getZipCode());
        self::assertTrue($company->getCreatedAt() < new DateTimeImmutable());
        self::assertTrue($company->getUpdatedAt() < new DateTimeImmutable());
    }
}
