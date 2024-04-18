<?php

declare(strict_types=1);

namespace App\Tests\Integration\Infrastructure\Controller\Fragments\Event\Participant;

use App\Tests\Integration\Infrastructure\Controller\AbstractWebTestCase;
use App\Tests\SessionHelper;

final class DeleteParticipantFragmentControllerTest extends AbstractWebTestCase
{
    use SessionHelper;

    public function testDelete(): void
    {
        $client = $this->login();
        $crawler = $client->request('DELETE', '/_fragments/events/f1f992d3-3cf5-4eb2-9b83-f112b7234613/participants/0faf6d38-6887-44b9-9896-7877e31c56c4/delete', [
            'token' => $this->generateCsrfToken($client, 'delete-participant'),
        ]);

        $this->assertResponseStatusCodeSame(200);
        $streams = $crawler->filter('turbo-stream');

        $this->assertSame($streams->eq(0)->attr('action'), 'remove');
        $this->assertSame($streams->eq(0)->attr('target'), 'block_participant_0faf6d38-6887-44b9-9896-7877e31c56c4');
    }

    public function testParticipantNotBelongsToEvent(): void
    {
        $client = $this->login();
        $client->request('DELETE', '/_fragments/events/f1f992d3-3cf5-4eb2-9b83-f112b7234613/participants/e4095f02-1516-42b3-82d1-506f2e74f027/delete', [
            'token' => $this->generateCsrfToken($client, 'delete-participant'),
        ]);

        $this->assertResponseStatusCodeSame(403);
    }

    public function testParticpantNotFound(): void
    {
        $client = $this->login();
        $client->request('DELETE', '/_fragments/events/f1f992d3-3cf5-4eb2-9b83-f112b7234613/participants/3ccb2b35-04a5-45d2-bcf5-2dcb1eda76dc/delete', [
            'token' => $this->generateCsrfToken($client, 'delete-participant'),
        ]);

        $this->assertResponseStatusCodeSame(404);
    }

    public function testEventNotBelongsToUser(): void
    {
        $client = $this->login('raphael.marchois@gmail.com');
        $client->request('DELETE', '/_fragments/events/f1f992d3-3cf5-4eb2-9b83-f112b7234613/participants/0faf6d38-6887-44b9-9896-7877e31c56c4/delete', [
            'token' => $this->generateCsrfToken($client, 'delete-participant'),
        ]);

        $this->assertResponseStatusCodeSame(403);
    }

    public function testEventNotFound(): void
    {
        $client = $this->login();
        $client->request('DELETE', '/_fragments/events/e2c992d3-3df5-0000-1234-f112b7234613/participants/0faf6d38-6887-44b9-9896-7877e31c56c4/delete', [
            'token' => $this->generateCsrfToken($client, 'delete-participant'),
        ]);

        $this->assertResponseStatusCodeSame(404);
    }

    public function testInvalidUri(): void
    {
        $client = $this->login();
        $client->request('DELETE', '/_fragments/events/aa-aa-aa-aa-aa/participants/aa-aa-aa-aa-aa/delete');

        $this->assertResponseStatusCodeSame(404);
    }

    public function testInvalidCsrfToken(): void
    {
        $client = $this->login();
        $client->request('DELETE', '/_fragments/events/f1f992d3-3cf5-4eb2-9b83-f112b7234613/participants/3ccb2b35-04a5-45d2-bcf5-2dcb1eda76dc/delete', [
            'token' => 'abc',
        ]);
        $this->assertResponseStatusCodeSame(400);
    }

    public function testWithoutAuthenticatedUser(): void
    {
        $client = static::createClient();
        $client->request('DELETE', '/_fragments/events/f1f992d3-3cf5-4eb2-9b83-f112b7234613/participants/0faf6d38-6887-44b9-9896-7877e31c56c4/delete');
        $this->assertResponseRedirects('http://localhost/login', 302);
    }
}
