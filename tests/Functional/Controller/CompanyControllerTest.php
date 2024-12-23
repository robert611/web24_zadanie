<?php

declare(strict_types=1);

namespace App\Tests\Functional\Controller;

use App\Repository\CompanyRepository;
use App\Tests\Fixtures\Fixtures;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

final class CompanyControllerTest extends WebTestCase
{
    private KernelBrowser $client;
    private Fixtures $fixtures;
    private CompanyRepository $companyRepository;

    public function setUp(): void
    {
        $this->client = self::createClient();
        $this->fixtures = self::getContainer()->get(Fixtures::class);
        $this->companyRepository = self::getContainer()->get(CompanyRepository::class);
    }

    public function testList(): void
    {
        // given
        $this->fixtures->aCompany(
            "Mercedes",
            "9876543210",
            "Parkowa 7a",
            "Warszawa",
            "10-733",
        );

        $this->fixtures->aCompany(
            "Leroy Merlin",
            "1234598760",
            "Witosa 255/13",
            "Lublin",
            "20-456",
        );

        // when
        $this->client->request('GET', '/api/companies');

        $responseContent = json_decode($this->client->getResponse()->getContent(), true);

        // then
        self::assertResponseIsSuccessful();
        self::assertCount(2, $responseContent);

        // and then (first company data is the same)
        self::assertEquals('Mercedes', $responseContent[0]['name']);
        self::assertEquals('9876543210', $responseContent[0]['nip']);
        self::assertEquals('Parkowa 7a', $responseContent[0]['address']);
        self::assertEquals('Warszawa', $responseContent[0]['city']);
        self::assertEquals('10-733', $responseContent[0]['zipCode']);

        // and then (second company data is the same)
        self::assertEquals('Leroy Merlin', $responseContent[1]['name']);
        self::assertEquals('1234598760', $responseContent[1]['nip']);
        self::assertEquals('Witosa 255/13', $responseContent[1]['address']);
        self::assertEquals('Lublin', $responseContent[1]['city']);
        self::assertEquals('20-456', $responseContent[1]['zipCode']);
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

    /**
     * @dataProvider provideNewCompanyEmptyPayloads
     */
    public function testIfCompanyNewPayloadValidation(array $payload, string $expectedMessage): void
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
        self::assertEquals($expectedMessage, $responseContent);
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
        self::assertEquals('Payload cannot be empty', $responseContent);
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
        self::assertEquals('Invalid json payload', $responseContent);
    }

    public static function provideNewCompanyEmptyPayloads(): array
    {
        return [
            [
                'payload' => [
                    'name' => '',
                ],
                'expectedMessage' => 'Name cannot be null or empty',
            ],
            [
                'payload' => [
                    'name' => 'Temu',
                ],
                'expectedMessage' => 'Nip cannot be null or empty',
            ],
            [
                'payload' => [
                    'name' => 'Temu',
                    'nip' => '1234567890',
                ],
                'expectedMessage' => 'Address cannot be null or empty',
            ],
            [
                'payload' => [
                    'name' => 'Temu',
                    'nip' => '1234567890',
                    'address' => 'Lisi Ogon',
                ],
                'expectedMessage' => 'City cannot be null or empty',
            ],
            [
                'payload' => [
                    'name' => 'Temu',
                    'nip' => '1234567890',
                    'address' => 'Lisi Ogon',
                    'city' => 'Lublin',
                ],
                'expectedMessage' => 'Zip code cannot be null or empty',
            ],
            [
                'payload' => [
                    'name' => 'Te',
                    'nip' => '1234567890',
                    'address' => 'Lisi Ogon',
                    'city' => 'Lublin',
                    'zipCode' => '25-555',
                ],
                'expectedMessage' => 'Name must contain at least 3 characters.',
            ],
            [
                'payload' => [
                    'name' => str_repeat('1', 256),
                    'nip' => '1234567890',
                    'address' => 'Lisi Ogon',
                    'city' => 'Lublin',
                    'zipCode' => '25-555',
                ],
                'expectedMessage' => 'Name must contain maximum 255 characters.',
            ],
            [
                'payload' => [
                    'name' => 'Temu',
                    'nip' => '12345678',
                    'address' => 'Lisi Ogon',
                    'city' => 'Lublin',
                    'zipCode' => '25-555',
                ],
                'expectedMessage' => 'Nip must consist of exactly ten digits.',
            ],
            [
                'payload' => [
                    'name' => 'Temu',
                    'nip' => '1234567890',
                    'address' => 'Li',
                    'city' => 'Lublin',
                    'zipCode' => '25-555',
                ],
                'expectedMessage' => 'Address must contain at least 3 characters.',
            ],
            [
                'payload' => [
                    'name' => 'Temu',
                    'nip' => '1234567890',
                    'address' => str_repeat('1', 256),
                    'city' => 'Lublin',
                    'zipCode' => '25-555',
                ],
                'expectedMessage' => 'Address must contain maximum 255 characters.',
            ],
            [
                'payload' => [
                    'name' => 'Temu',
                    'nip' => '1234567890',
                    'address' => 'Lisi ogon',
                    'city' => 'L',
                    'zipCode' => '25-555',
                ],
                'expectedMessage' => 'City name must contain at least 2 characters.',
            ],
            [
                'payload' => [
                    'name' => 'Temu',
                    'nip' => '1234567890',
                    'address' => 'Lisi ogon',
                    'city' => str_repeat('1', 65),
                    'zipCode' => '25-555',
                ],
                'expectedMessage' => 'City name must contain maximum 64 characters.',
            ],
            [
                'payload' => [
                    'name' => 'Temu',
                    'nip' => '1234567890',
                    'address' => 'Lisi ogon',
                    'city' => 'Lublin',
                    'zipCode' => '254-555',
                ],
                'expectedMessage' => 'Zip code must be in XX-XXX format.',
            ],
        ];
    }
}
