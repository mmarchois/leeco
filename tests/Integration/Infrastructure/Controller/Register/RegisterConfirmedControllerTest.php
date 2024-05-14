<?php

declare(strict_types=1);

namespace App\Tests\Integration\Infrastructure\Controller\Register;

use App\Tests\Integration\Infrastructure\Controller\AbstractWebTestCase;

final class RegisterConfirmedControllerTest extends AbstractWebTestCase
{
    public function testRegisterSucceeded(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/register/confirmed');

        $this->assertResponseStatusCodeSame(200);
        $this->assertSecurityHeaders();
        $this->assertSame('Compte vérifié', $crawler->filter('h1')->text());
        $this->assertMetaTitle('Compte vérifié - Leeco', $crawler);
    }
}
