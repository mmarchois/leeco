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

        $this->assertResponseStatusCodeSame(200);
        $this->assertSecurityHeaders();
        $this->assertMetaTitle('Vos évènements - Moment', $crawler);
        $this->assertSame('Vos évènements', $crawler->filter('h1')->text());
        $this->assertSame(2, $table->filter('tr')->count());

        $tr1 = $table->filter('tr')->eq(0)->filter('td');
        $link1 = $tr1->eq(2)->filter('a');
        $this->assertSame('Mariage A&A', $tr1->eq(0)->text());
        $this->assertSame('05/05/2023', $tr1->eq(1)->text());
        $this->assertSame('Voir', $link1->text());
        $this->assertSame('http://localhost/app/events/2203014c-5d51-4e20-b607-2b48ffb3f0c7', $link1->link()->getUri());

        $tr2 = $table->filter('tr')->eq(1)->filter('td');
        $link2 = $tr2->eq(2)->filter('a');
        $this->assertSame('Mariage H&M', $tr2->eq(0)->text());
        $this->assertSame('05/01/2019', $tr2->eq(1)->text());
        $this->assertSame('Voir', $link2->text());
        $this->assertSame('http://localhost/app/events/f1f992d3-3cf5-4eb2-9b83-f112b7234613', $link2->link()->getUri());
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
