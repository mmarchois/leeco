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
        $this->assertSame('Tags de l\'évènement "Mariage H&M" (2)', $crawler->filter('h1')->text());
        $this->assertMetaTitle('Tags de l\'évènement "Mariage H&M" - Moment', $crawler);

        $this->assertSame(2, $table->filter('tr')->count());

        $tr1 = $table->filter('tr')->eq(0)->filter('td');
        $this->assertSame('Cérémonie religieuse', $tr1->eq(0)->text());
        $this->assertSame('05/01/2019 19:00', $tr1->eq(1)->text());
        $this->assertSame('05/01/2019 21:00', $tr1->eq(2)->text());

        $tr2 = $table->filter('tr')->eq(1)->filter('td');
        $this->assertSame('Dîner', $tr2->eq(0)->text());
        $this->assertSame('05/01/2019 21:01', $tr2->eq(1)->text());
        $this->assertSame('05/01/2019 23:30', $tr2->eq(2)->text());
    }

    public function testListOtherEvent(): void
    {
        $client = $this->login();
        $crawler = $client->request('GET', '/app/events/2203014c-5d51-4e20-b607-2b48ffb3f0c7/tags');
        $table = $crawler->filter('[data-testid="tag-list"] tbody');

        $this->assertResponseStatusCodeSame(200);
        $this->assertSecurityHeaders();
        $this->assertSame('Tags de l\'évènement "EVG Julien" (1)', $crawler->filter('h1')->text());
        $this->assertMetaTitle('Tags de l\'évènement "EVG Julien" - Moment', $crawler);

        $this->assertSame(1, $table->filter('tr')->count());

        $tr1 = $table->filter('tr')->eq(0)->filter('td');
        $this->assertSame('Bubble foot', $tr1->eq(0)->text());
        $this->assertSame('05/05/2023 08:00', $tr1->eq(1)->text());
        $this->assertSame('10/05/2023 23:00', $tr1->eq(2)->text());
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
