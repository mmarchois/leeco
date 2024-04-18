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
        $this->assertSame('Participants de l\'évènement "Mariage H&M" (2)', $crawler->filter('h1')->text());
        $this->assertMetaTitle('Participants de l\'évènement "Mariage H&M" - Moment', $crawler);

        $this->assertSame(2, $table->filter('tr')->count());

        $tr1 = $table->filter('tr')->eq(0)->filter('td');
        $this->assertSame('Tony & Corinne', $tr1->eq(0)->text());
        $this->assertSame('MARCHOIS', $tr1->eq(1)->text());
        $this->assertSame('tc.marchois@gmail.com', $tr1->eq(2)->text());
        $this->assertSame('accessCode1 - Non envoyé', $tr1->eq(3)->text());

        $formDelete1 = $tr1->selectButton('Supprimer')->form();
        $this->assertSame($formDelete1->getUri(), 'http://localhost/_fragments/events/f1f992d3-3cf5-4eb2-9b83-f112b7234613/participants/0faf6d38-6887-44b9-9896-7877e31c56c4/delete');
        $this->assertSame($formDelete1->getMethod(), 'DELETE');

        $tr2 = $table->filter('tr')->eq(1)->filter('td');
        $this->assertSame('Floran', $tr2->eq(0)->text());
        $this->assertSame('ROISIN', $tr2->eq(1)->text());
        $this->assertSame('floran.roisin@gmail.com', $tr2->eq(2)->text());
        $this->assertSame('accessCode2 - Envoyé', $tr2->eq(3)->text());

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
        $this->assertSame('Participants de l\'évènement "Mariage A&A" (1)', $crawler->filter('h1')->text());
        $this->assertMetaTitle('Participants de l\'évènement "Mariage A&A" - Moment', $crawler);

        $this->assertSame(1, $table->filter('tr')->count());

        $tr1 = $table->filter('tr')->eq(0)->filter('td');
        $this->assertSame('Anais', $tr1->eq(0)->text());
        $this->assertSame('MARCHOIS', $tr1->eq(1)->text());
        $this->assertSame('anais.marchois@gmail.com', $tr1->eq(2)->text());
        $this->assertSame('accessCode3 - Non envoyé', $tr1->eq(3)->text());

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
