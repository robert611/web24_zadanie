<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\CompanyRepository;
use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\Table;
use JsonSerializable;

#[ORM\Entity(repositoryClass: CompanyRepository::class)]
#[Table(name: 'company')]
class Company implements JsonSerializable
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: 'id', type: 'integer', nullable: false)]
    private int $id;

    #[ORM\Column(name: "name", type: 'string', length: 255, nullable: false)]
    private string $name;

    #[ORM\Column(name: "nip", type: 'string', length: 10, nullable: false)]
    private string $nip;

    #[ORM\Column(name: "address", type: 'string', length: 255, nullable: false)]
    private string $address;

    #[ORM\Column(name: "city", type: 'string', length: 64, nullable: false)]
    private string $city;

    #[ORM\Column(name: "zip_code", type: 'string', length: 6, nullable: false)]
    private string $zipCode;

    #[ORM\Column(name: "created_at", type: 'datetime_immutable', nullable: false)]
    private DateTimeImmutable $createdAt;

    #[ORM\Column(name: "updated_at", type: 'datetime_immutable', nullable: false)]
    private DateTimeImmutable $updatedAt;

    public function getId(): int
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getNip(): string
    {
        return $this->nip;
    }

    public function getAddress(): string
    {
        return $this->address;
    }

    public function getCity(): string
    {
        return $this->city;
    }

    public function getZipCode(): string
    {
        return $this->zipCode;
    }

    public function getCreatedAt(): DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function getUpdatedAt(): DateTimeImmutable
    {
        return $this->updatedAt;
    }

    public static function create(string $name, string $nip, string $address, string $city, string $zipCode): self
    {
        $company = new Company();
        $company->name = $name;
        $company->nip = $nip;
        $company->address = $address;
        $company->city = $city;
        $company->zipCode = $zipCode;
        $company->createdAt = new DateTimeImmutable();
        $company->updatedAt = new DateTimeImmutable();

        return $company;
    }

    /**
     * @return array<string, mixed>
     */
    public function jsonSerialize(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'nip' => $this->nip,
            'address' => $this->address,
            'city' => $this->city,
            'zipCode' => $this->zipCode,
        ];
    }
}