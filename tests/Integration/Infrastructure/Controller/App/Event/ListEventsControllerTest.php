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

        $tr1 = $table->filter('tr')->eq(0);
        $this->assertSame('Mariage A&A', $tr1->filter('td')->eq(0)->text());
        $this->assertSame('05/05/2023', $tr1->filter('td')->eq(1)->text());
        $this->assertSame('Action', $tr1->filter('td')->eq(2)->text());

        $tr2 = $table->filter('tr')->eq(1);
        $this->assertSame('Mariage H&M', $tr2->filter('td')->eq(0)->text());
        $this->assertSame('05/01/2019', $tr2->filter('td')->eq(1)->text());
        $this->assertSame('Action', $tr2->filter('td')->eq(2)->text());
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
