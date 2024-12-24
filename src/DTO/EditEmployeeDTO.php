<?php

declare(strict_types=1);

namespace App\DTO;

use Symfony\Component\Validator\Constraints as Assert;

class EditEmployeeDTO
{
    #[Assert\NotBlank]
    #[Assert\Length(min: 3, max: 86)]
    private ?string $firstName;

    #[Assert\NotBlank]
    #[Assert\Length(min: 3, max: 86)]
    private ?string $lastName;

    #[Assert\NotBlank]
    #[Assert\Email]
    private ?string $email;

    #[Assert\NotBlank(
        message: 'Phone number cannot be blank if provided.',
        allowNull: true,
    )]
    #[Assert\Regex(
        pattern: '/^\+48 \d{3} \d{3} \d{3}$/',
        message: 'Phone number must be a valid Polish number in the format "+48 123 456 789".',
    )]
    private ?string $phoneNumber = null;

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

    public static function create(
        ?string $firstName,
        ?string $lastName,
        ?string $email,
        ?string $phoneNumber = null,
    ): EditEmployeeDTO {
        $employeeDTO = new self();
        $employeeDTO->firstName = $firstName;
        $employeeDTO->lastName = $lastName;
        $employeeDTO->email = $email;
        $employeeDTO->phoneNumber = $phoneNumber;

        return $employeeDTO;
    }
}
