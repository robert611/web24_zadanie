<?php

declare(strict_types=1);

namespace App\Tests\Functional\Controller;

use App\Tests\Fixtures\Fixtures;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

final class EmployeeControllerTest extends WebTestCase
{
    private KernelBrowser $client;
    private Fixtures $fixtures;

    public function setUp(): void
    {
        $this->client = self::createClient();
        $this->fixtures = self::getContainer()->get(Fixtures::class);
    }

    public function testList(): void
    {
        // given
        $company = $this->fixtures->aCompany(
            "Mercedes",
            "9876543210",
            "Parkowa 7a",
            "Warszawa",
            "10-733",
        );

        $employee1 = $this->fixtures->anEmployee(
            $company,
            'Paul',
            'Johnson',
            'paul.johnson@example.com',
            '573458910',
        );

        $employee2 = $this->fixtures->anEmployee(
            $company,
            'Mike',
            'Watson',
            'mike.watson@example.com',
        );

        // when
        $this->client->request('GET', '/api/employees');

        $responseContent = json_decode($this->client->getResponse()->getContent(), true);

        // then
        self::assertResponseIsSuccessful();
        self::assertCount(2, $responseContent);

        // and then (first employee data is the same)
        self::assertEquals($employee1->getId(), $responseContent[0]['id']);
        self::assertEquals('Paul', $responseContent[0]['firstName']);
        self::assertEquals('Johnson', $responseContent[0]['lastName']);
        self::assertEquals('paul.johnson@example.com', $responseContent[0]['email']);
        self::assertEquals('573458910', $responseContent[0]['phoneNumber']);

        // and then (second employee data is the same)
        self::assertEquals($employee2->getId(), $responseContent[1]['id']);
        self::assertEquals('Mike', $responseContent[1]['firstName']);
        self::assertEquals('Watson', $responseContent[1]['lastName']);
        self::assertEquals('mike.watson@example.com', $responseContent[1]['email']);
        self::assertEquals(null, $responseContent[1]['phoneNumber']);

        // and then (company data is the same)
        self::assertEquals($company->getId(), $responseContent[0]['company']['id']);
        self::assertEquals('Mercedes', $responseContent[0]['company']['name']);
        self::assertEquals('9876543210', $responseContent[0]['company']['nip']);
        self::assertEquals('Parkowa 7a', $responseContent[0]['company']['address']);
        self::assertEquals('Warszawa', $responseContent[0]['company']['city']);
        self::assertEquals('10-733', $responseContent[0]['company']['zipCode']);
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

        $employee = $this->fixtures->anEmployee(
            $company,
            "John",
            "Mack",
            "john.mack@example.com",
            "+48 345 678 123"
        );

        // when
        $this->client->request('GET', "/api/employees/{$employee->getId()}");

        $responseContent = json_decode($this->client->getResponse()->getContent(), true);

        // then
        self::assertResponseIsSuccessful();
        self::assertEquals($company->getId(), $responseContent['company']['id']);
        self::assertEquals('John', $responseContent['firstName']);
        self::assertEquals('Mack', $responseContent['lastName']);
        self::assertEquals('john.mack@example.com', $responseContent['email']);
        self::assertEquals('+48 345 678 123', $responseContent['phoneNumber']);
    }

    /**
     * @test
     */
    public function willHandle404ForSingleEmployee(): void
    {
        // when
        $this->client->request('GET', "/api/employees/100");

        $responseContent = json_decode($this->client->getResponse()->getContent(), true);

        // then
        self::assertEquals(Response::HTTP_NOT_FOUND, $this->client->getResponse()->getStatusCode());
        self::assertEquals(['message' => 'Employee not found'], $responseContent['developerMessage']);
        self::assertEquals(['message' => 'Employee not found'], $responseContent['userMessage']);
        self::assertEquals(Response::HTTP_NOT_FOUND, $responseContent['errorCode']);
        self::assertEquals('Please look into api/doc for more information.', $responseContent['moreInfo']);
    }
}
