<?php

declare(strict_types=1);

namespace App\Tests\Integration\Infrastructure\Controller\Register;

use App\Tests\Integration\Infrastructure\Controller\AbstractWebTestCase;

final class RegisterSucceededControllerTest extends AbstractWebTestCase
{
    public function testRegisterSucceeded(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/register/succeeded?email=mathieu.marchois@gmail.com');

        $this->assertResponseStatusCodeSame(200);
        $this->assertSecurityHeaders();
        $this->assertSame('Vérifiez vos emails', $crawler->filter('h1')->text());
        $this->assertMetaTitle('Vérifiez vos emails - Leeco', $crawler);
    }

    public function testMissingEmail(): void
    {
        $client = static::createClient();
        $client->request('GET', '/register/succeeded');

        $this->assertResponseStatusCodeSame(404);
    }
}
