<?php

declare(strict_types=1);

namespace App\Tests\Fixtures;

use App\Entity\Company;
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
}
