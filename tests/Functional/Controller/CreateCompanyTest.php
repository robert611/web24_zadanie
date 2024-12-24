<?php

declare(strict_types=1);

namespace App\Tests\Functional\Controller;

use App\Repository\CompanyRepository;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

final class CreateCompanyTest extends WebTestCase
{
    private KernelBrowser $client;
    private CompanyRepository $companyRepository;

    public function setUp(): void
    {
        $this->client = self::createClient();
        $this->companyRepository = self::getContainer()->get(CompanyRepository::class);
    }

    /**
     * @test
     */
    public function canCreateNewCompany(): void
    {
        // when
        $this->client->request(
            method: 'POST',
            uri: 'api/companies',
            server: [
                'content-type' => 'application/json',
                'accept' => 'application/json',
            ],
            content: json_encode([
                'name' => 'Miller and Johnson',
                'nip' => '9876543210',
                'address' => 'Drzewna 7a',
                'city' => 'Biała podlaska',
                'zipCode' => '53-733',
            ]),
        );

        // then
        $responseContent = json_decode($this->client->getResponse()->getContent(), true);

        self::assertResponseIsSuccessful();
        self::assertEquals('Miller and Johnson', $responseContent['name']);
        self::assertEquals('9876543210', $responseContent['nip']);
        self::assertEquals('Drzewna 7a', $responseContent['address']);
        self::assertEquals('Biała podlaska', $responseContent['city']);
        self::assertEquals('53-733', $responseContent['zipCode']);

        // and then (company is in fact in database)
        self::assertEquals(1, $this->companyRepository->count());
        self::assertEquals($responseContent['id'], $this->companyRepository->findOneBy([])->getId());
    }

    public function testIfPayloadCannotBeEmpty(): void
    {
        // when
        $this->client->request(
            method: 'POST',
            uri: 'api/companies',
            server: [
                'content-type' => 'application/json',
                'accept' => 'application/json',
            ],
        );

        // then
        $responseContent = json_decode($this->client->getResponse()->getContent(), true);

        self::assertEquals(Response::HTTP_BAD_REQUEST, $this->client->getResponse()->getStatusCode());
        self::assertEquals([
            'developerMessage' => 'Payload cannot be empty',
            'userMessage' => 'Payload cannot be empty',
            'errorCode' => Response::HTTP_BAD_REQUEST,
            'moreInfo' => 'Please look into api/doc for more information.',
        ], $responseContent);
    }

    public function testIfPayloadMustBeValid(): void
    {
        // when
        $this->client->request(
            method: 'POST',
            uri: 'api/companies',
            server: [
                'content-type' => 'application/json',
                'accept' => 'application/json',
            ],
            content: '{',
        );

        // then
        $responseContent = json_decode($this->client->getResponse()->getContent(), true);

        self::assertEquals(Response::HTTP_BAD_REQUEST, $this->client->getResponse()->getStatusCode());
        self::assertEquals([
            'developerMessage' => 'Invalid json payload',
            'userMessage' => 'Invalid json payload',
            'errorCode' => Response::HTTP_BAD_REQUEST,
            'moreInfo' => 'Please look into api/doc for more information.',
        ], $responseContent);
    }

    /**
     * @dataProvider provideNewCompanyEmptyPayloads
     * @param array<string, mixed> $expectedResponse
     * @param array<string, string> $payload
     */
    public function testNewCompanyPayloadValidation(array $payload, array $expectedResponse): void
    {
        // when
        $this->client->request(
            method: 'POST',
            uri: 'api/companies',
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
    public static function provideNewCompanyEmptyPayloads(): array
    {
        return [
            [
                'payload' => [
                    'name' => '',
                ],
                'expectedResponse' => [
                    'developerMessage' => 'Name cannot be null or empty',
                    'userMessage' => 'Name cannot be null or empty',
                    'errorCode' => Response::HTTP_BAD_REQUEST,
                    'moreInfo' => 'Please look into api/doc for more information.'
                ],
            ],
            [
                'payload' => [
                    'name' => 'Temu',
                ],
                'expectedResponse' => [
                    'developerMessage' => 'Nip cannot be null or empty',
                    'userMessage' => 'Nip cannot be null or empty',
                    'errorCode' => Response::HTTP_BAD_REQUEST,
                    'moreInfo' => 'Please look into api/doc for more information.'
                ],
            ],
            [
                'payload' => [
                    'name' => 'Temu',
                    'nip' => '1234567890',
                ],
                'expectedResponse' => [
                    'developerMessage' => 'Address cannot be null or empty',
                    'userMessage' => 'Address cannot be null or empty',
                    'errorCode' => Response::HTTP_BAD_REQUEST,
                    'moreInfo' => 'Please look into api/doc for more information.'
                ],
            ],
            [
                'payload' => [
                    'name' => 'Temu',
                    'nip' => '1234567890',
                    'address' => 'Lisi Ogon',
                ],
                'expectedResponse' => [
                    'developerMessage' => 'City cannot be null or empty',
                    'userMessage' => 'City cannot be null or empty',
                    'errorCode' => Response::HTTP_BAD_REQUEST,
                    'moreInfo' => 'Please look into api/doc for more information.'
                ],
            ],
            [
                'payload' => [
                    'name' => 'Temu',
                    'nip' => '1234567890',
                    'address' => 'Lisi Ogon',
                    'city' => 'Lublin',
                ],
                'expectedResponse' => [
                    'developerMessage' => 'Zip code cannot be null or empty',
                    'userMessage' => 'Zip code cannot be null or empty',
                    'errorCode' => Response::HTTP_BAD_REQUEST,
                    'moreInfo' => 'Please look into api/doc for more information.'
                ],
            ],
            [
                'payload' => [
                    'name' => 'Te',
                    'nip' => '1234567890',
                    'address' => 'Lisi Ogon',
                    'city' => 'Lublin',
                    'zipCode' => '25-555',
                ],
                'expectedResponse' => [
                    'developerMessage' => 'Name must contain at least 3 characters.',
                    'userMessage' => 'Name must contain at least 3 characters.',
                    'errorCode' => Response::HTTP_BAD_REQUEST,
                    'moreInfo' => 'Please look into api/doc for more information.'
                ],
            ],
            [
                'payload' => [
                    'name' => str_repeat('1', 256),
                    'nip' => '1234567890',
                    'address' => 'Lisi Ogon',
                    'city' => 'Lublin',
                    'zipCode' => '25-555',
                ],
                'expectedResponse' => [
                    'developerMessage' => 'Name must contain maximum 255 characters.',
                    'userMessage' => 'Name must contain maximum 255 characters.',
                    'errorCode' => Response::HTTP_BAD_REQUEST,
                    'moreInfo' => 'Please look into api/doc for more information.'
                ],
            ],
            [
                'payload' => [
                    'name' => 'Temu',
                    'nip' => '12345678',
                    'address' => 'Lisi Ogon',
                    'city' => 'Lublin',
                    'zipCode' => '25-555',
                ],
                'expectedResponse' => [
                    'developerMessage' => 'Nip must consist of exactly ten digits.',
                    'userMessage' => 'Nip must consist of exactly ten digits.',
                    'errorCode' => Response::HTTP_BAD_REQUEST,
                    'moreInfo' => 'Please look into api/doc for more information.'
                ],
            ],
            [
                'payload' => [
                    'name' => 'Temu',
                    'nip' => '1234567890',
                    'address' => 'Li',
                    'city' => 'Lublin',
                    'zipCode' => '25-555',
                ],
                'expectedResponse' => [
                    'developerMessage' => 'Address must contain at least 3 characters.',
                    'userMessage' => 'Address must contain at least 3 characters.',
                    'errorCode' => Response::HTTP_BAD_REQUEST,
                    'moreInfo' => 'Please look into api/doc for more information.'
                ],
            ],
            [
                'payload' => [
                    'name' => 'Temu',
                    'nip' => '1234567890',
                    'address' => str_repeat('1', 256),
                    'city' => 'Lublin',
                    'zipCode' => '25-555',
                ],
                'expectedResponse' => [
                    'developerMessage' => 'Address must contain maximum 255 characters.',
                    'userMessage' => 'Address must contain maximum 255 characters.',
                    'errorCode' => Response::HTTP_BAD_REQUEST,
                    'moreInfo' => 'Please look into api/doc for more information.'
                ],
            ],
            [
                'payload' => [
                    'name' => 'Temu',
                    'nip' => '1234567890',
                    'address' => 'Lisi ogon',
                    'city' => 'L',
                    'zipCode' => '25-555',
                ],
                'expectedResponse' => [
                    'developerMessage' => 'City name must contain at least 2 characters.',
                    'userMessage' => 'City name must contain at least 2 characters.',
                    'errorCode' => Response::HTTP_BAD_REQUEST,
                    'moreInfo' => 'Please look into api/doc for more information.'
                ],
            ],
            [
                'payload' => [
                    'name' => 'Temu',
                    'nip' => '1234567890',
                    'address' => 'Lisi ogon',
                    'city' => str_repeat('1', 65),
                    'zipCode' => '25-555',
                ],
                'expectedResponse' => [
                    'developerMessage' => 'City name must contain maximum 64 characters.',
                    'userMessage' => 'City name must contain maximum 64 characters.',
                    'errorCode' => Response::HTTP_BAD_REQUEST,
                    'moreInfo' => 'Please look into api/doc for more information.'
                ],
            ],
            [
                'payload' => [
                    'name' => 'Temu',
                    'nip' => '1234567890',
                    'address' => 'Lisi ogon',
                    'city' => 'Lublin',
                    'zipCode' => '254-555',
                ],
                'expectedResponse' => [
                    'developerMessage' => 'Zip code must be in XX-XXX format.',
                    'userMessage' => 'Zip code must be in XX-XXX format.',
                    'errorCode' => Response::HTTP_BAD_REQUEST,
                    'moreInfo' => 'Please look into api/doc for more information.'
                ],
            ],
        ];
    }
}
