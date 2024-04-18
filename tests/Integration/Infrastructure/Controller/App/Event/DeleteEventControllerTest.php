<?php

declare(strict_types=1);

namespace App\Tests\Integration\Infrastructure\Controller\App\Event;

use App\Tests\Integration\Infrastructure\Controller\AbstractWebTestCase;
use App\Tests\SessionHelper;

final class DeleteEventControllerTest extends AbstractWebTestCase
{
    use SessionHelper;

    public function testDelete(): void
    {
        $client = $this->login();
        $client->request('DELETE', '/app/events/f1f992d3-3cf5-4eb2-9b83-f112b7234613/delete', [
            'token' => $this->generateCsrfToken($client, 'delete-event'),
        ]);
        $this->assertResponseStatusCodeSame(302);
        $client->followRedirect();

        $this->assertResponseStatusCodeSame(200);
        $this->assertRouteSame('app_events_list');
    }

    public function testDeleteAnEventNotOwned(): void
    {
        $client = $this->login('raphael.marchois@gmail.com');
        $client->request('DELETE', '/app/events/f1f992d3-3cf5-4eb2-9b83-f112b7234613/delete', [
            'token' => $this->generateCsrfToken($client, 'delete-event'),
        ]);

        $this->assertResponseStatusCodeSame(403);
    }

    public function testDeleteEventNotFound(): void
    {
        $client = $this->login();
        $client->request('DELETE', '/app/events/1d288130-7317-42b6-b2a7-7fd6cd0918de/delete', [
            'token' => $this->generateCsrfToken($client, 'delete-event'),
        ]);

        $this->assertResponseStatusCodeSame(404);
    }

    public function testDeleteInvalidUri(): void
    {
        $client = $this->login();
        $client->request('DELETE', '/app/events/aa-aa-aa-aa-aa/delete');

        $this->assertResponseStatusCodeSame(404);
    }

    public function testWithoutAuthenticatedUser(): void
    {
        $client = static::createClient();
        $client->request('DELETE', '/app/events/f1f992d3-3cf5-4eb2-9b83-f112b7234613/delete');
        $this->assertResponseRedirects('http://localhost/login', 302);
    }
}
