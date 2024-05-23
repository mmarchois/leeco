<?php

declare(strict_types=1);

namespace App\Tests\Integration\Infrastructure\Controller\App\Event\Tag;

use App\Tests\Integration\Infrastructure\Controller\AbstractWebTestCase;

final class ListTagsControllerTest extends AbstractWebTestCase
{
    public function testList(): void
    {
        $client = $this->login();
        $crawler = $client->request('GET', '/app/events/f1f992d3-3cf5-4eb2-9b83-f112b7234613/tags');
        $table = $crawler->filter('[data-testid="tag-list"] tbody');

        $this->assertResponseStatusCodeSame(200);
        $this->assertSecurityHeaders();
        $this->assertSame('Catégories', $crawler->filter('h1')->text());
        $this->assertMetaTitle('Catégories - Leeco', $crawler);

        $this->assertBreadcrumbStructure([
            ['Mon espace', ['href' => '/app']],
            ['Mes évènements', ['href' => '/app/events']],
            ['Mariage H&M', ['href' => '/app/events/f1f992d3-3cf5-4eb2-9b83-f112b7234613']],
            ['Catégories', ['href' => null]],
        ], $crawler);

        $this->assertSame(2, $table->filter('tr')->count());

        $tr1 = $table->filter('tr')->eq(0)->filter('td');
        $link1 = $tr1->eq(4)->filter('a');
        $this->assertSame('Cérémonie religieuse', $tr1->eq(0)->text());
        $this->assertSame('0', $tr1->eq(1)->text());
        $this->assertSame('05/01/2019 à 19h00', $tr1->eq(2)->text());
        $this->assertSame('05/01/2019 à 21h00', $tr1->eq(3)->text());

        $this->assertSame('Modifier', $link1->eq(0)->text());
        $this->assertSame('http://localhost/app/events/f1f992d3-3cf5-4eb2-9b83-f112b7234613/tags/4d2bfad5-2e55-4059-ba43-783acb237772/edit', $link1->eq(0)->link()->getUri());

        $tr2 = $table->filter('tr')->eq(1)->filter('td');
        $link2 = $tr2->eq(4)->filter('a');
        $this->assertSame('Dîner', $tr2->eq(0)->text());
        $this->assertSame('0', $tr2->eq(1)->text());
        $this->assertSame('05/01/2019 à 20h00', $tr2->eq(2)->text());
        $this->assertSame('05/01/2019 à 22h00', $tr2->eq(3)->text());

        $this->assertSame('Modifier', $link2->eq(0)->text());
        $this->assertSame('http://localhost/app/events/f1f992d3-3cf5-4eb2-9b83-f112b7234613/tags/95af7a78-8a14-4f82-b41a-66ef01cbd603/edit', $link2->eq(0)->link()->getUri());
    }

    public function testListOtherEvent(): void
    {
        $client = $this->login();
        $crawler = $client->request('GET', '/app/events/2203014c-5d51-4e20-b607-2b48ffb3f0c7/tags');
        $table = $crawler->filter('[data-testid="tag-list"] tbody');

        $this->assertSame(1, $table->filter('tr')->count());

        $tr1 = $table->filter('tr')->eq(0)->filter('td');
        $link1 = $tr1->eq(4)->filter('a');
        $this->assertSame('Bubble foot', $tr1->eq(0)->text());
        $this->assertSame('0', $tr1->eq(1)->text());
        $this->assertSame('05/05/2023 à 08h00', $tr1->eq(2)->text());
        $this->assertSame('10/05/2023 à 23h00', $tr1->eq(3)->text());

        $this->assertSame('Modifier', $link1->eq(0)->text());
        $this->assertSame('http://localhost/app/events/2203014c-5d51-4e20-b607-2b48ffb3f0c7/tags/9abf583d-8128-4da1-a359-736cfd3d13db/edit', $link1->eq(0)->link()->getUri());
    }

    public function testAccessToAnEventNotOwned(): void
    {
        $client = $this->login('raphael.marchois@gmail.com');
        $client->request('GET', '/app/events/f1f992d3-3cf5-4eb2-9b83-f112b7234613/tags');

        $this->assertResponseStatusCodeSame(403);
    }

    public function testEventNotFound(): void
    {
        $client = $this->login();
        $client->request('GET', '/app/events/1d288130-7317-42b6-b2a7-7fd6cd0918de/tags');

        $this->assertResponseStatusCodeSame(404);
    }

    public function testInvalidUri(): void
    {
        $client = $this->login();
        $client->request('GET', '/app/events/aa-aa-aa-aa-aa/tags');

        $this->assertResponseStatusCodeSame(404);
    }

    public function testBadPageSizeParameter(): void
    {
        $client = $this->login();

        $client->request('GET', '/app/events/1d288130-7317-42b6-b2a7-7fd6cd0918de/tags/1?pageSize=0');
        $this->assertResponseStatusCodeSame(400);

        $client->request('GET', '/app/events/1d288130-7317-42b6-b2a7-7fd6cd0918de/tags/1?pageSize=test');
        $this->assertResponseStatusCodeSame(400);
    }

    public function testWithoutAuthenticatedUser(): void
    {
        $client = static::createClient();
        $client->request('GET', '/app/events/f1f992d3-3cf5-4eb2-9b83-f112b7234613/tags');
        $this->assertResponseRedirects('http://localhost/login', 302);
    }
}
