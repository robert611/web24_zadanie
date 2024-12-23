<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\CompanyRepository;
use DateTimeImmutable;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\Table;
use JsonSerializable;
use Symfony\Component\Serializer\Annotation\Groups;
use OpenApi\Attributes as OA;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: CompanyRepository::class)]
#[Table(name: 'company')]
class Company implements JsonSerializable
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: 'id', type: 'integer', nullable: false)]
    #[Groups(['company_read', 'employee_read'])]
    private int $id;

    #[ORM\Column(name: 'name', type: 'string', length: 255, nullable: false)]
    #[Groups(['company_read', 'employee_read'])]
    #[Assert\NotBlank(message: 'Company name cannot be empty.')]
    #[Assert\Length(
        min: 3,
        max: 255,
        minMessage: 'Name must contain at least {{ limit }} characters.',
        maxMessage: 'Name must contain maximum {{ limit }} characters.',
    )]
    #[OA\Property(description: 'Name must contain at least three, and maximum of 255 characters.')]
    private string $name;

    #[ORM\Column(name: 'nip', type: 'string', length: 10, nullable: false)]
    #[Groups(['company_read', 'employee_read'])]
    #[Assert\NotBlank(message: 'Nip cannot be empty.')]
    #[Assert\Regex(
        pattern: '/^\d{10}$/',
        message: 'Nip must consist of exactly ten digits.'
    )]
    #[OA\Property(description: 'Nip must consist of ten digits without dashes in between.')]
    private string $nip;

    #[ORM\Column(name: 'address', type: 'string', length: 255, nullable: false)]
    #[Groups(['company_read', 'employee_read'])]
    #[Assert\NotBlank(message: 'Address cannot be empty.')]
    #[Assert\Length(
        min: 3,
        max: 255,
        minMessage: 'Address must contain at least {{ limit }} characters.',
        maxMessage: 'Address must contain maximum {{ limit }} characters.',
    )]
    #[OA\Property(description: 'Address must contain at least three, and maximum of 255 characters.')]
    private string $address;

    #[ORM\Column(name: "city", type: 'string', length: 64, nullable: false)]
    #[Groups(['company_read', 'employee_read'])]
    #[Assert\NotBlank(message: "City name cannot be empty.")]
    #[Assert\Length(
        min: 2,
        max: 64,
        minMessage: 'City name must contain at least {{ limit }} characters.',
        maxMessage: 'City name must contain maximum {{ limit }} characters.',
    )]
    #[OA\Property(description: 'City name must contain at least two, and maximum of 64 characters.')]
    private string $city;

    #[ORM\Column(name: 'zip_code', type: 'string', length: 6, nullable: false)]
    #[Groups(['company_read', 'employee_read'])]
    #[Assert\NotBlank(message: "Zip code cannot be empty.")]
    #[Assert\Regex(
        pattern: '/^\d{2}-\d{3}$/',
        message: "Zip code must be in XX-XXX format."
    )]
    #[OA\Property(description: 'Zip code must be in XX-XXX format.')]
    private string $zipCode;

    #[ORM\Column(name: 'created_at', type: 'datetime_immutable', nullable: false)]
    private DateTimeImmutable $createdAt;

    #[ORM\Column(name: 'updated_at', type: 'datetime_immutable', nullable: false)]
    private DateTimeImmutable $updatedAt;

    #[ORM\OneToMany(targetEntity: Employee::class, mappedBy: 'company', cascade: ['persist', 'remove'])]
    private Collection $employees;

    public function __construct()
    {
        $this->employees = new ArrayCollection();
    }

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

    public function getEmployees(): Collection
    {
        return $this->employees;
    }

    public function addEmployee(Employee $employee): void
    {
        if (!$this->employees->contains($employee)) {
            $this->employees->add($employee);
            $employee->addToCompany($this);
        }
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

    public function update(string $name, string $nip, string $address, string $city, string $zipCode): void
    {
        $this->name = $name;
        $this->nip = $nip;
        $this->address = $address;
        $this->city = $city;
        $this->zipCode = $zipCode;
        $this->updatedAt = new DateTimeImmutable();
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
