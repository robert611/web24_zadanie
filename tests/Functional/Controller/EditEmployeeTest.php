<?php

declare(strict_types=1);

namespace App\Tests\Functional\Controller;

use App\Repository\EmployeeRepository;
use App\Tests\Fixtures\Fixtures;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

final class EditEmployeeTest extends WebTestCase
{
    private KernelBrowser $client;
    private Fixtures $fixtures;
    private EmployeeRepository $employeeRepository;

    public function setUp(): void
    {
        $this->client = self::createClient([], [
            'CONTENT_TYPE' => 'application/json',
            'HTTP_ACCEPT' => 'application/json',
        ]);
        $this->fixtures = self::getContainer()->get(Fixtures::class);
        $this->employeeRepository = self::getContainer()->get(EmployeeRepository::class);
    }

    /**
     * @test
     */
    public function canEditEmployee(): void
    {
        // given
        $company = $this->fixtures->aCompany(
            "Mercedes",
            "9876543210",
            "Parkowa 7a",
            "Warszawa",
            "10-733",
        );

        $employee = $this->fixtures->anEmployee(
            $company,
            "John",
            "Doe",
            "john.doe@example.com",
            "+48 444 666 434"
        );

        // when
        $this->client->request(
            method: 'PUT',
            uri: "api/employees/{$employee->getId()}",
            server: [
                'HTTP_CONTENT_TYPE' => 'application/json',
                'HTTP_ACCEPT' => 'application/json',
            ],
            content: json_encode([
                'firstName' => 'Miller',
                'lastName' => 'Douglas',
                'email' => 'miller.douglas@gmail.com',
                'phoneNumber' => '+48 675 888 906',
            ]),
        );

        $responseContent = json_decode($this->client->getResponse()->getContent(), true);

        // then
        self::assertResponseIsSuccessful();

        // and then
        self::assertEquals('Miller', $responseContent['firstName']);
        self::assertEquals('Douglas', $responseContent['lastName']);
        self::assertEquals('miller.douglas@gmail.com', $responseContent['email']);
        self::assertEquals('+48 675 888 906', $responseContent['phoneNumber']);

        // and then
        self::assertEquals($company->getId(), $responseContent['company']['id']);
        self::assertEquals($company->getName(), $responseContent['company']['name']);
        self::assertEquals($company->getNip(), $responseContent['company']['nip']);
        self::assertEquals($company->getAddress(), $responseContent['company']['address']);
        self::assertEquals($company->getCity(), $responseContent['company']['city']);
        self::assertEquals($company->getZipCode(), $responseContent['company']['zipCode']);

        // and then
        $employee = $this->employeeRepository->findOneBy([]);
        self::assertEquals(1, $this->employeeRepository->count());
        self::assertEquals($company->getId(), $employee->getCompany()->getId());
        self::assertEquals('Miller', $employee->getFirstName());
        self::assertEquals('Douglas', $employee->getLastName());
        self::assertEquals('miller.douglas@gmail.com', $employee->getEmail());
        self::assertEquals('+48 675 888 906', $employee->getPhoneNumber());
    }

    public function testIfPayloadCannotBeEmpty(): void
    {
        // given
        $company = $this->fixtures->aCompany(
            "Mercedes",
            "9876543210",
            "Parkowa 7a",
            "Warszawa",
            "10-733",
        );

        $employee = $this->fixtures->anEmployee(
            $company,
            "John",
            "Doe",
            "john.doe@example.com",
            "+48 444 666 434"
        );

        // when
        $this->client->request(
            method: 'PUT',
            uri: "api/employees/{$employee->getId()}",
            server: [
                'HTTP_CONTENT_TYPE' => 'application/json',
                'HTTP_ACCEPT' => 'application/json',
            ],
        );

        // then
        $responseContent = json_decode($this->client->getResponse()->getContent(), true);

        self::assertEquals(Response::HTTP_BAD_REQUEST, $this->client->getResponse()->getStatusCode());
        self::assertEquals([
            'developerMessage' => [
                ['message' => 'Payload cannot be empty'],
            ],
            'userMessage' => [
                ['message' => 'Payload cannot be empty'],
            ],
            'errorCode' => Response::HTTP_BAD_REQUEST,
            'moreInfo' => 'Please look into api/doc for more information.',
        ], $responseContent);
    }

    public function testIfPayloadMustBeValid(): void
    {
        // given
        $company = $this->fixtures->aCompany(
            "Mercedes",
            "9876543210",
            "Parkowa 7a",
            "Warszawa",
            "10-733",
        );

        $employee = $this->fixtures->anEmployee(
            $company,
            "John",
            "Doe",
            "john.doe@example.com",
            "+48 444 666 434"
        );

        // when
        $this->client->request(
            method: 'PUT',
            uri: "api/employees/{$employee->getId()}",
            server: [
                'HTTP_CONTENT_TYPE' => 'application/json',
                'HTTP_ACCEPT' => 'application/json',
            ],
            content: '{',
        );

        // then
        $responseContent = json_decode($this->client->getResponse()->getContent(), true);

        self::assertEquals(Response::HTTP_BAD_REQUEST, $this->client->getResponse()->getStatusCode());
        self::assertEquals([
            'developerMessage' => [
                ['message' => 'Invalid json payload'],
            ],
            'userMessage' => [
                ['message' => 'Invalid json payload'],
            ],
            'errorCode' => Response::HTTP_BAD_REQUEST,
            'moreInfo' => 'Please look into api/doc for more information.',
        ], $responseContent);
    }

    /**
     * @dataProvider provideEditEmployeeEmptyPayloads
     * @param array<string, mixed> $expectedResponse
     * @param array<string, string> $payload
     */
    public function testEditEmployeePayloadValidation(array $payload, array $expectedResponse): void
    {
        // given
        $company = $this->fixtures->aCompany(
            "Mercedes",
            "9876543210",
            "Parkowa 7a",
            "Warszawa",
            "10-733",
        );

        $employee = $this->fixtures->anEmployee(
            $company,
            "John",
            "Doe",
            "john.doe@example.com",
            "+48 444 666 434",
        );

        // when
        $this->client->request(
            method: 'PUT',
            uri: "api/employees/{$employee->getId()}",
            server: [
                'content-type' => 'application/json',
                'accept' => 'application/json',
            ],
            content: json_encode($payload),
        );

        // then
        $responseContent = json_decode($this->client->getResponse()->getContent(), true);

        self::assertEquals(Response::HTTP_BAD_REQUEST, $this->client->getResponse()->getStatusCode());
        self::assertEquals($expectedResponse, $responseContent);
    }

    /**
     * @return array<int, array<string, array<string, mixed>>>
     */
    public static function provideEditEmployeeEmptyPayloads(): array
    {
        return [
            [
                'payload' => [
                    'company' => '',
                    'firstName' => '',
                    'lastName' => '',
                    'email' => '',
                ],
                'expectedResponse' => [
                    'developerMessage' => [
                        [
                            'field' => 'firstName',
                            'message' => 'This value should not be blank.',
                        ],
                        [
                            'field' => 'firstName',
                            'message' => 'This value is too short. It should have 3 characters or more.',
                        ],
                        [
                            'field' => 'lastName',
                            'message' => 'This value should not be blank.',
                        ],
                        [
                            'field' => 'lastName',
                            'message' => 'This value is too short. It should have 3 characters or more.',
                        ],
                        [
                            'field' => 'email',
                            'message' => 'This value should not be blank.',
                        ],
                    ],
                    'userMessage' => [
                        [
                            'field' => 'firstName',
                            'message' => 'This value should not be blank.',
                        ],
                        [
                            'field' => 'firstName',
                            'message' => 'This value is too short. It should have 3 characters or more.',
                        ],
                        [
                            'field' => 'lastName',
                            'message' => 'This value should not be blank.',
                        ],
                        [
                            'field' => 'lastName',
                            'message' => 'This value is too short. It should have 3 characters or more.',
                        ],
                        [
                            'field' => 'email',
                            'message' => 'This value should not be blank.',
                        ],
                    ],
                    'errorCode' => Response::HTTP_BAD_REQUEST,
                    'moreInfo' => 'Please look into api/doc for more information.'
                ],
            ],
            [
                'payload' => [
                    'firstName' => 'John',
                    'lastName' => 'Doe',
                    'email' => 'wrong.email@',
                    'phoneNumber' => '',
                ],
                'expectedResponse' => [
                    'developerMessage' => [
                        [
                            'field' => 'email',
                            'message' => 'This value is not a valid email address.',
                        ],
                        [
                            'field' => 'phoneNumber',
                            'message' => 'Phone number cannot be blank if provided.',
                        ],
                    ],
                    'userMessage' => [
                        [
                            'field' => 'email',
                            'message' => 'This value is not a valid email address.',
                        ],
                        [
                            'field' => 'phoneNumber',
                            'message' => 'Phone number cannot be blank if provided.',
                        ],
                    ],
                    'errorCode' => Response::HTTP_BAD_REQUEST,
                    'moreInfo' => 'Please look into api/doc for more information.',
                ],
            ],
            [
                'payload' => [
                    'firstName' => 'John',
                    'lastName' => 'Doe',
                    'email' => 'john.doe@gmail.com',
                    'phoneNumber' => '+48 33 33 444',
                ],
                'expectedResponse' => [
                    'developerMessage' => [
                        [
                            'field' => 'phoneNumber',
                            'message' => 'Phone number must be a valid Polish number in the format "+48 123 456 789".',
                        ],
                    ],
                    'userMessage' => [
                        [
                            'field' => 'phoneNumber',
                            'message' => 'Phone number must be a valid Polish number in the format "+48 123 456 789".',
                        ],
                    ],
                    'errorCode' => Response::HTTP_BAD_REQUEST,
                    'moreInfo' => 'Please look into api/doc for more information.',
                ],
            ],
        ];
    }
}
