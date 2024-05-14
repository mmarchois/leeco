<?php

declare(strict_types=1);

namespace App\Tests\Integration\Infrastructure\Controller\App\Event;

use App\Tests\Integration\Infrastructure\Controller\AbstractWebTestCase;

final class DashboardEventControllerTest extends AbstractWebTestCase
{
    public function testDashboard(): void
    {
        $client = $this->login();
        $crawler = $client->request('GET', '/app/events/f1f992d3-3cf5-4eb2-9b83-f112b7234613');

        $this->assertResponseStatusCodeSame(200);
        $this->assertSecurityHeaders();
        $this->assertSame('Mariage H&M', $crawler->filter('h1')->text());
        $this->assertMetaTitle('Mariage H&M - Leeco', $crawler);

        $this->assertBreadcrumbStructure([
            ['Mon espace', ['href' => '/app']],
            ['Mes évènements', ['href' => '/app/events']],
            ['Mariage H&M', ['href' => null]],
        ], $crawler);

        $formDelete = $crawler->selectButton('Supprimer')->form();
        $this->assertSame($formDelete->getUri(), 'http://localhost/app/events/f1f992d3-3cf5-4eb2-9b83-f112b7234613/delete');
        $this->assertSame($formDelete->getMethod(), 'DELETE');
    }

    public function testAccessToAnEventNotOwned(): void
    {
        $client = $this->login('raphael.marchois@gmail.com');
        $client->request('GET', '/app/events/f1f992d3-3cf5-4eb2-9b83-f112b7234613');

        $this->assertResponseStatusCodeSame(403);
    }

    public function testEventNotFound(): void
    {
        $client = $this->login();
        $client->request('GET', '/app/events/1d288130-7317-42b6-b2a7-7fd6cd0918de');

        $this->assertResponseStatusCodeSame(404);
    }

    public function testInvalidUri(): void
    {
        $client = $this->login();
        $client->request('GET', '/app/events/aa-aa-aa-aa-aa');

        $this->assertResponseStatusCodeSame(404);
    }

    public function testWithoutAuthenticatedUser(): void
    {
        $client = static::createClient();
        $client->request('GET', '/app/events/f1f992d3-3cf5-4eb2-9b83-f112b7234613');
        $this->assertResponseRedirects('http://localhost/login', 302);
    }
}
