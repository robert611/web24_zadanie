<?php

declare(strict_types=1);

namespace App\Tests\Fixtures;

use App\Entity\Company;
use App\Entity\Employee;
use Doctrine\ORM\EntityManagerInterface;

class Fixtures
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
    ) {
    }

    public function aCompany(string $name, string $nip, string $address, string $city, string $zipCode): Company
    {
        $company = Company::create(
            $name,
            $nip,
            $address,
            $city,
            $zipCode,
        );

        $this->entityManager->persist($company);
        $this->entityManager->flush();

        return $company;
    }

    public function anEmployee(
        Company $company,
        string $firstName,
        string $lastName,
        string $email,
        ?string $phoneNumber = null,
    ): Employee {
        $employee = Employee::create(
            $company,
            $firstName,
            $lastName,
            $email,
            $phoneNumber,
        );

        $this->entityManager->persist($employee);
        $this->entityManager->flush();

        return $employee;
    }
}
