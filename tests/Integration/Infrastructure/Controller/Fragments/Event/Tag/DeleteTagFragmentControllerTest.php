<?php

declare(strict_types=1);

namespace App\Tests\Integration\Infrastructure\Controller\Fragments\Event\Tag;

use App\Tests\Integration\Infrastructure\Controller\AbstractWebTestCase;
use App\Tests\SessionHelper;

final class DeleteTagFragmentControllerTest extends AbstractWebTestCase
{
    use SessionHelper;

    public function testDelete(): void
    {
        $client = $this->login();
        $crawler = $client->request('DELETE', '/_fragments/events/f1f992d3-3cf5-4eb2-9b83-f112b7234613/tags/4d2bfad5-2e55-4059-ba43-783acb237772/delete', [
            'token' => $this->generateCsrfToken($client, 'delete-tag'),
        ]);

        $this->assertResponseStatusCodeSame(200);
        $streams = $crawler->filter('turbo-stream');

        $this->assertSame($streams->eq(0)->attr('action'), 'remove');
        $this->assertSame($streams->eq(0)->attr('target'), 'block_tag_4d2bfad5-2e55-4059-ba43-783acb237772');
    }

    public function testTagNotBelongsToEvent(): void
    {
        $client = $this->login();
        $client->request('DELETE', '/_fragments/events/f1f992d3-3cf5-4eb2-9b83-f112b7234613/tags/9abf583d-8128-4da1-a359-736cfd3d13db/delete', [
            'token' => $this->generateCsrfToken($client, 'delete-tag'),
        ]);

        $this->assertResponseStatusCodeSame(403);
    }

    public function testTagNotFound(): void
    {
        $client = $this->login();
        $client->request('DELETE', '/_fragments/events/f1f992d3-3cf5-4eb2-9b83-f112b7234613/tags/86839b28-7e69-4a20-85ca-3d38224d96f9/delete', [
            'token' => $this->generateCsrfToken($client, 'delete-tag'),
        ]);

        $this->assertResponseStatusCodeSame(404);
    }

    public function testEventNotBelongsToUser(): void
    {
        $client = $this->login('raphael.marchois@gmail.com');
        $client->request('DELETE', '/_fragments/events/f1f992d3-3cf5-4eb2-9b83-f112b7234613/tags/4d2bfad5-2e55-4059-ba43-783acb237772/delete', [
            'token' => $this->generateCsrfToken($client, 'delete-tag'),
        ]);

        $this->assertResponseStatusCodeSame(403);
    }

    public function testEventNotFound(): void
    {
        $client = $this->login();
        $client->request('DELETE', '/_fragments/events/e2c992d3-3df5-0000-1234-f112b7234613/tags/4d2bfad5-2e55-4059-ba43-783acb237772/delete', [
            'token' => $this->generateCsrfToken($client, 'delete-tag'),
        ]);

        $this->assertResponseStatusCodeSame(404);
    }

    public function testInvalidUri(): void
    {
        $client = $this->login();
        $client->request('DELETE', '/_fragments/events/aa-aa-aa-aa-aa/tags/aa-aa-aa-aa-aa/delete');

        $this->assertResponseStatusCodeSame(404);
    }

    public function testInvalidCsrfToken(): void
    {
        $client = $this->login();
        $client->request('DELETE', '/_fragments/events/f1f992d3-3cf5-4eb2-9b83-f112b7234613/tags/3ccb2b35-04a5-45d2-bcf5-2dcb1eda76dc/delete', [
            'token' => 'abc',
        ]);
        $this->assertResponseStatusCodeSame(400);
    }

    public function testWithoutAuthenticatedUser(): void
    {
        $client = static::createClient();
        $client->request('DELETE', '/_fragments/events/f1f992d3-3cf5-4eb2-9b83-f112b7234613/tags/0faf6d38-6887-44b9-9896-7877e31c56c4/delete');
        $this->assertResponseRedirects('http://localhost/login', 302);
    }
}
