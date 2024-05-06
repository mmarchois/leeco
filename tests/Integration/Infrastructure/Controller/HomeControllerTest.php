<?php

declare(strict_types=1);

namespace App\Tests\Integration\Infrastructure\Controller;

final class HomeControllerTest extends AbstractWebTestCase
{
    public function testHome(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/');

        $this->assertResponseStatusCodeSame(200);
        $this->assertSecurityHeaders();
        $this->assertMetaTitle('Moment', $crawler);

        $this->assertNavStructure([
            ['Moment', ['href' => '/']],
            ['Se connecter', ['href' => '/login']],
            ['Essayer gratuitement', ['href' => '/register']],
        ], $crawler);
    }

    public function testHomeLogged(): void
    {
        $client = $this->login();
        $crawler = $client->request('GET', '/');
        $this->assertNavStructure([
            ['Moment', ['href' => '/']],
            ['Mon espace', ['href' => '/app']],
        ], $crawler);
    }
}
