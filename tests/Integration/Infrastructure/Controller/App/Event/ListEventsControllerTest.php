<?php

declare(strict_types=1);

namespace App\Tests\Integration\Infrastructure\Controller\App\Event;

use App\Tests\Integration\Infrastructure\Controller\AbstractWebTestCase;

final class ListEventsControllerTest extends AbstractWebTestCase
{
    public function testListEvents(): void
    {
        $client = $this->login();
        $crawler = $client->request('GET', '/app/events');
        $table = $crawler->filter('[data-testid="event-list"] tbody');

        $this->assertBreadcrumbStructure([
            ['Mon espace', ['href' => '/app']],
            ['Mes évènements', ['href' => null]],
        ], $crawler);

        $this->assertResponseStatusCodeSame(200);
        $this->assertSecurityHeaders();
        $this->assertMetaTitle('Mes évènements - Leeco', $crawler);
        $this->assertSame('Mes évènements', $crawler->filter('h1')->text());
        $this->assertSame(2, $table->filter('tr')->count());

        $tr = $table->filter('tr')->eq(0)->filter('td');
        $link = $tr->eq(3)->filter('a');
        $this->assertSame('Mariage H&M', $tr->eq(0)->text());
        $this->assertSame('FR123456789', $tr->eq(1)->text());
        $this->assertSame('du 05/01/2019 au 07/01/2019', $tr->eq(2)->text());
        $this->assertSame('Voir', $link->eq(0)->text());
        $this->assertSame('http://localhost/app/events/f1f992d3-3cf5-4eb2-9b83-f112b7234613', $link->eq(0)->link()->getUri());

        $tr1 = $table->filter('tr')->eq(1)->filter('td');
        $link1 = $tr1->eq(3)->filter('a');
        $this->assertSame('EVG Julien', $tr1->eq(0)->text());
        $this->assertSame('FR76556789', $tr1->eq(1)->text());
        $this->assertSame('le 05/05/2023', $tr1->eq(2)->text());
        $this->assertSame('Voir', $link1->eq(0)->text());
        $this->assertSame('http://localhost/app/events/2203014c-5d51-4e20-b607-2b48ffb3f0c7', $link1->eq(0)->link()->getUri());
    }

    public function testListEventsWithOtherUser(): void
    {
        $client = $this->login('raphael.marchois@gmail.com');
        $crawler = $client->request('GET', '/app/events');
        $table = $crawler->filter('[data-testid="event-list"] tbody');

        $this->assertResponseStatusCodeSame(200);
        $this->assertSame(1, $table->filter('tr')->count());
        $this->assertSame('Vous n\'avez pas encore d\'évènement.', $table->filter('tr')->eq(0)->text());
    }

    public function testBadPageParameter(): void
    {
        $client = $this->login();
        $client->request('GET', '/app/events/test');

        $this->assertResponseStatusCodeSame(404);
    }

    public function testBadPageSizeParameter(): void
    {
        $client = $this->login();

        $client->request('GET', '/app/events/1?pageSize=0');
        $this->assertResponseStatusCodeSame(400);

        $client->request('GET', '/app/events/1?pageSize=test');
        $this->assertResponseStatusCodeSame(400);
    }

    public function testWithoutAuthenticatedUser(): void
    {
        $client = static::createClient();
        $client->request('GET', '/app');

        $this->assertResponseRedirects('http://localhost/login', 302);
    }
}
