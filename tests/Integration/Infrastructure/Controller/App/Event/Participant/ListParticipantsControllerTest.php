<?php

declare(strict_types=1);

namespace App\Tests\Integration\Infrastructure\Controller\App\Event\Participant;

use App\Tests\Integration\Infrastructure\Controller\AbstractWebTestCase;

final class ListParticipantsControllerTest extends AbstractWebTestCase
{
    public function testList(): void
    {
        $client = $this->login();
        $crawler = $client->request('GET', '/app/events/f1f992d3-3cf5-4eb2-9b83-f112b7234613/participants');
        $table = $crawler->filter('[data-testid="participant-list"] tbody');

        $this->assertResponseStatusCodeSame(200);
        $this->assertSecurityHeaders();
        $this->assertSame('Participants', $crawler->filter('h1')->text());
        $this->assertMetaTitle('Participants - Moment', $crawler);

        $this->assertBreadcrumbStructure([
            ['Mon espace', ['href' => '/app']],
            ['Mes évènements', ['href' => '/app/events']],
            ['Mariage H&M', ['href' => '/app/events/f1f992d3-3cf5-4eb2-9b83-f112b7234613']],
            ['Participants', ['href' => null]],
        ], $crawler);

        $this->assertSame(2, $table->filter('tr')->count());

        $tr1 = $table->filter('tr')->eq(0)->filter('td');
        $link1 = $tr1->eq(5)->filter('a');
        $this->assertSame('MARCHOIS', $tr1->eq(0)->text());
        $this->assertSame('Tony & Corinne', $tr1->eq(1)->text());
        $this->assertSame('tc.marchois@gmail.com', $tr1->eq(2)->text());
        $this->assertSame('accessCode1', $tr1->eq(3)->text());
        $this->assertSame('Non envoyé', $tr1->eq(4)->text());
        $this->assertSame('Modifier', $link1->eq(0)->text());
        $this->assertSame('http://localhost/app/events/f1f992d3-3cf5-4eb2-9b83-f112b7234613/participants/0faf6d38-6887-44b9-9896-7877e31c56c4/edit', $link1->eq(0)->link()->getUri());

        $formDelete1 = $tr1->selectButton('Supprimer')->form();
        $this->assertSame($formDelete1->getUri(), 'http://localhost/_fragments/events/f1f992d3-3cf5-4eb2-9b83-f112b7234613/participants/0faf6d38-6887-44b9-9896-7877e31c56c4/delete');
        $this->assertSame($formDelete1->getMethod(), 'DELETE');

        $tr2 = $table->filter('tr')->eq(1)->filter('td');
        $link2 = $tr2->eq(5)->filter('a');
        $this->assertSame('ROISIN', $tr2->eq(0)->text());
        $this->assertSame('Floran', $tr2->eq(1)->text());
        $this->assertSame('floran.roisin@gmail.com', $tr2->eq(2)->text());
        $this->assertSame('accessCode2', $tr2->eq(3)->text());
        $this->assertSame('Envoyé', $tr2->eq(4)->text());
        $this->assertSame('Modifier', $link2->eq(0)->text());
        $this->assertSame('http://localhost/app/events/f1f992d3-3cf5-4eb2-9b83-f112b7234613/participants/6f6973d5-6733-415e-bd35-432a6b50f8cf/edit', $link2->eq(0)->link()->getUri());

        $formDelete2 = $tr2->selectButton('Supprimer')->form();
        $this->assertSame($formDelete2->getUri(), 'http://localhost/_fragments/events/f1f992d3-3cf5-4eb2-9b83-f112b7234613/participants/6f6973d5-6733-415e-bd35-432a6b50f8cf/delete');
        $this->assertSame($formDelete2->getMethod(), 'DELETE');
    }

    public function testListOtherEvent(): void
    {
        $client = $this->login();
        $crawler = $client->request('GET', '/app/events/2203014c-5d51-4e20-b607-2b48ffb3f0c7/participants');
        $table = $crawler->filter('[data-testid="participant-list"] tbody');

        $this->assertResponseStatusCodeSame(200);
        $this->assertSecurityHeaders();
        $this->assertSame('Participants', $crawler->filter('h1')->text());
        $this->assertMetaTitle('Participants - Moment', $crawler);

        $this->assertSame(1, $table->filter('tr')->count());

        $tr1 = $table->filter('tr')->eq(0)->filter('td');
        $link1 = $tr1->eq(5)->filter('a');

        $this->assertSame('MARCHOIS', $tr1->eq(0)->text());
        $this->assertSame('Julien', $tr1->eq(1)->text());
        $this->assertSame('julien.marchois@gmail.com', $tr1->eq(2)->text());
        $this->assertSame('accessCode3', $tr1->eq(3)->text());
        $this->assertSame('Non envoyé', $tr1->eq(4)->text());

        $this->assertSame('Modifier', $link1->eq(0)->text());
        $this->assertSame('http://localhost/app/events/2203014c-5d51-4e20-b607-2b48ffb3f0c7/participants/e4095f02-1516-42b3-82d1-506f2e74f027/edit', $link1->eq(0)->link()->getUri());

        $formDelete1 = $tr1->selectButton('Supprimer')->form();
        $this->assertSame($formDelete1->getUri(), 'http://localhost/_fragments/events/2203014c-5d51-4e20-b607-2b48ffb3f0c7/participants/e4095f02-1516-42b3-82d1-506f2e74f027/delete');
        $this->assertSame($formDelete1->getMethod(), 'DELETE');
    }

    public function testAccessToAnEventNotOwned(): void
    {
        $client = $this->login('raphael.marchois@gmail.com');
        $client->request('GET', '/app/events/f1f992d3-3cf5-4eb2-9b83-f112b7234613/participants');

        $this->assertResponseStatusCodeSame(403);
    }

    public function testEventNotFound(): void
    {
        $client = $this->login();
        $client->request('GET', '/app/events/1d288130-7317-42b6-b2a7-7fd6cd0918de/participants');

        $this->assertResponseStatusCodeSame(404);
    }

    public function testInvalidUri(): void
    {
        $client = $this->login();
        $client->request('GET', '/app/events/aa-aa-aa-aa-aa/participants');

        $this->assertResponseStatusCodeSame(404);
    }

    public function testBadPageSizeParameter(): void
    {
        $client = $this->login();

        $client->request('GET', '/app/events/1d288130-7317-42b6-b2a7-7fd6cd0918de/participants/1?pageSize=0');
        $this->assertResponseStatusCodeSame(400);

        $client->request('GET', '/app/events/1d288130-7317-42b6-b2a7-7fd6cd0918de/participants/1?pageSize=test');
        $this->assertResponseStatusCodeSame(400);
    }

    public function testWithoutAuthenticatedUser(): void
    {
        $client = static::createClient();
        $client->request('GET', '/app/events/f1f992d3-3cf5-4eb2-9b83-f112b7234613/participants');
        $this->assertResponseRedirects('http://localhost/login', 302);
    }
}
