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
    public function canShowSingleCompany(): void
    {
        // given
        $company = $this->fixtures->aCompany(
            "Marco Polo",
            "6574839201",
            "Lubelska 7a",
            "Elbląg",
            "35-733",
        );

        // when
        $this->client->request('GET', "/api/companies/{$company->getId()}");

        $responseContent = json_decode($this->client->getResponse()->getContent(), true);

        // then
        self::assertResponseIsSuccessful();
        self::assertEquals($company->getId(), $responseContent['id']);
        self::assertEquals('Marco Polo', $responseContent['name']);
        self::assertEquals('6574839201', $responseContent['nip']);
        self::assertEquals('Lubelska 7a', $responseContent['address']);
        self::assertEquals('Elbląg', $responseContent['city']);
        self::assertEquals('35-733', $responseContent['zipCode']);
    }

    /**
     * @test
     */
    public function willHandle404ForSingleCompany(): void
    {
        // when
        $this->client->request('GET', "/api/companies/100");

        $responseContent = json_decode($this->client->getResponse()->getContent(), true);

        // then
        self::assertEquals(Response::HTTP_NOT_FOUND, $this->client->getResponse()->getStatusCode());
        self::assertEquals('Company not found', $responseContent['developerMessage']);
        self::assertEquals('Company not found', $responseContent['userMessage']);
        self::assertEquals(Response::HTTP_NOT_FOUND, $responseContent['errorCode']);
        self::assertEquals('Please look into api/doc for more information.', $responseContent['moreInfo']);
    }

    /**
     * @test
     */
    public function canDeleteCompany(): void
    {
        // given
        $company = $this->fixtures->aCompany(
            "Marco Polo",
            "6574839201",
            "Lubelska 7a",
            "Elbląg",
            "35-733",
        );

        // when
        $this->client->request('DELETE', "/api/companies/{$company->getId()}");

        json_decode($this->client->getResponse()->getContent(), true);

        // then
        self::assertResponseIsSuccessful();

        // and then
        self::assertEquals(0, $this->companyRepository->count());
    }

    /**
     * @test
     */
    public function willHandle404ForDeleteCompany(): void
    {
        // when
        $this->client->request('DELETE', "/api/companies/100");

        $responseContent = json_decode($this->client->getResponse()->getContent(), true);

        // then
        self::assertEquals(Response::HTTP_NOT_FOUND, $this->client->getResponse()->getStatusCode());
        self::assertEquals('Company not found', $responseContent['developerMessage']);
        self::assertEquals('Company not found', $responseContent['userMessage']);
        self::assertEquals(Response::HTTP_NOT_FOUND, $responseContent['errorCode']);
        self::assertEquals('Please look into api/doc for more information.', $responseContent['moreInfo']);
    }
}
