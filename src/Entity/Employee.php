<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\EmployeeRepository;
use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\Table;
use JsonSerializable;
use Symfony\Component\Serializer\Annotation\Groups;
use OpenApi\Attributes as OA;

#[ORM\Entity(repositoryClass: EmployeeRepository::class)]
#[Table(name: 'employee')]
class Employee implements JsonSerializable
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: 'id', type: 'integer', nullable: false)]
    #[Groups(['employee_read'])]
    private int $id;

    #[ORM\ManyToOne(targetEntity: Company::class, inversedBy: 'employees')]
    #[ORM\JoinColumn(name: 'company_id', referencedColumnName: 'id', nullable: false, onDelete: 'CASCADE')]
    #[Groups(['employee_read'])]
    private Company $company;

    #[ORM\Column(name: 'first_name', type: 'string', length: 86, nullable: false)]
    #[Groups(['employee_read'])]
    #[OA\Property(description: 'First name must contain at least three, and maximum of 86 characters.')]
    private string $firstName;

    #[ORM\Column(name: 'last_name', type: 'string', length: 86, nullable: false)]
    #[Groups(['employee_read'])]
    #[OA\Property(description: 'Last name must contain at least three, and maximum of 86 characters.')]
    private string $lastName;

    #[ORM\Column(name: 'email', type: 'string', length: 255, nullable: false)]
    #[Groups(['employee_read'])]
    private string $email;

    #[ORM\Column(name: 'phone_number', type: 'string', length: 64, nullable: true)]
    #[Groups(['employee_read'])]
    #[OA\Property(description: 'Phone number must be a valid Polish number in the format "+48 123 456 789".')]
    private ?string $phoneNumber = null;

    #[ORM\Column(name: 'created_at', type: 'datetime_immutable', nullable: false)]
    private DateTimeImmutable $createdAt;

    #[ORM\Column(name: 'updated_at', type: 'datetime_immutable', nullable: false)]
    private DateTimeImmutable $updatedAt;

    public function getId(): int
    {
        return $this->id;
    }

    public function getCompany(): Company
    {
        return $this->company;
    }

    public function getFirstName(): string
    {
        return $this->firstName;
    }

    public function getLastName(): string
    {
        return $this->lastName;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function getPhoneNumber(): ?string
    {
        return $this->phoneNumber;
    }

    public function getUpdatedAt(): DateTimeImmutable
    {
        return $this->updatedAt;
    }

    public function getCreatedAt(): DateTimeImmutable
    {
        return $this->createdAt;
    }

    public static function create(
        Company $company,
        string $firstName,
        string $lastName,
        string $email,
        ?string $phoneNumber = null,
    ): Employee {
        $employee = new self();
        $employee->company = $company;
        $employee->firstName = $firstName;
        $employee->lastName = $lastName;
        $employee->email = $email;
        $employee->phoneNumber = $phoneNumber;
        $employee->createdAt = new DateTimeImmutable();
        $employee->updatedAt = new DateTimeImmutable();

        return $employee;
    }

    public function addToCompany(Company $company): void
    {
        $this->company = $company;
    }

    /**
     * @return array<string, mixed>
     */
    public function jsonSerialize(): array
    {
        return [
            'id' => $this->id,
            'firstName' => $this->firstName,
            'lastName' => $this->lastName,
            'email' => $this->email,
            'phoneNumber' => $this->phoneNumber,
            'company' => $this->company->jsonSerialize(),
        ];
    }
}
