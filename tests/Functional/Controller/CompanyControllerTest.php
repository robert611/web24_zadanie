<?php

declare(strict_types=1);

namespace App\Tests\Functional\Controller;

use App\Tests\Fixtures\Fixtures;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

final class CompanyControllerTest extends WebTestCase
{
    private KernelBrowser $client;
    private Fixtures $fixtures;

    public function setUp(): void
    {
        $this->client = self::createClient();
        $this->fixtures = self::getContainer()->get(Fixtures::class);
    }

    public function testIndex(): void
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
        $this->client->request('GET', '/company/index');

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
}
