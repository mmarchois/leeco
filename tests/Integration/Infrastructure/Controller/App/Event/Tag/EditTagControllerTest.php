<?php

declare(strict_types=1);

namespace App\Tests\Integration\Infrastructure\Controller\App\Event\Tag;

use App\Tests\Integration\Infrastructure\Controller\AbstractWebTestCase;

final class EditTagControllerTest extends AbstractWebTestCase
{
    public function testEdit(): void
    {
        $client = $this->login();
        $crawler = $client->request('GET', '/app/events/f1f992d3-3cf5-4eb2-9b83-f112b7234613/tags/4d2bfad5-2e55-4059-ba43-783acb237772/edit');

        $this->assertResponseStatusCodeSame(200);
        $this->assertSecurityHeaders();
        $this->assertSame('Modifier "Cérémonie religieuse"', $crawler->filter('h1')->text());
        $this->assertMetaTitle('Modifier "Cérémonie religieuse" - Leeco', $crawler);

        $this->assertBreadcrumbStructure([
            ['Mon espace', ['href' => '/app']],
            ['Mes évènements', ['href' => '/app/events']],
            ['Mariage H&M', ['href' => '/app/events/f1f992d3-3cf5-4eb2-9b83-f112b7234613']],
            ['Catégories', ['href' => '/app/events/f1f992d3-3cf5-4eb2-9b83-f112b7234613/tags']],
            ['Modifier "Cérémonie religieuse"', ['href' => null]],
        ], $crawler);

        $saveButton = $crawler->selectButton('Sauvegarder');
        $form = $saveButton->form();
        $form['tag_form[title]'] = 'Cérémonie religieuse';
        $form['tag_form[startDate]'] = '2019-05-01 18:00';
        $form['tag_form[endDate]'] = '2019-05-01 20:00';
        $client->submit($form);
        $crawler = $client->followRedirect();
        $this->assertResponseStatusCodeSame(200);
        $this->assertRouteSame('app_tags_list');
        $this->assertSame(2, $crawler->filter('[data-testid="tag-list"] tbody tr')->count());
    }

    public function testBadValues(): void
    {
        $client = $this->login();
        $crawler = $client->request('GET', '/app/events/f1f992d3-3cf5-4eb2-9b83-f112b7234613/tags/4d2bfad5-2e55-4059-ba43-783acb237772/edit');

        $saveButton = $crawler->selectButton('Sauvegarder');

        // Empty values
        $form = $saveButton->form();
        $form['tag_form[title]'] = '';
        $crawler = $client->submit($form);
        $this->assertResponseStatusCodeSame(422);

        $this->assertSame('Cette valeur ne doit pas être vide.', $crawler->filter('#tag_form_title_error')->text());

        // Bad values
        $form['tag_form[title]'] = str_repeat('a', 101);

        $form['tag_form[startDate]'] = '2019-05-01 18:00';
        $form['tag_form[endDate]'] = '2019-05-01 10:00';

        $crawler = $client->submit($form);
        $this->assertResponseStatusCodeSame(422);

        $this->assertSame('Cette chaîne est trop longue. Elle doit avoir au maximum 100 caractères.', $crawler->filter('#tag_form_title_error')->text());
        $this->assertSame('La date de fin doit être supérieure à la date de début.', $crawler->filter('#tag_form_endDate_error')->text());
    }

    public function testTagAlreadyExist(): void
    {
        $client = $this->login();
        $crawler = $client->request('GET', '/app/events/f1f992d3-3cf5-4eb2-9b83-f112b7234613/tags/4d2bfad5-2e55-4059-ba43-783acb237772/edit');

        $saveButton = $crawler->selectButton('Sauvegarder');
        $form = $saveButton->form();

        $form['tag_form[title]'] = 'Dîner';

        $form['tag_form[startDate]'] = '2019-05-01 18:00';
        $form['tag_form[endDate]'] = '2019-05-01 20:00';

        $crawler = $client->submit($form);

        $this->assertResponseStatusCodeSame(422);
        $this->assertSame('Ce tag existe déjà.', $crawler->filter('#tag_form_title_error')->text());
    }

    public function testAccessToAnEventNotOwned(): void
    {
        $client = $this->login('raphael.marchois@gmail.com');
        $client->request('GET', '/app/events/f1f992d3-3cf5-4eb2-9b83-f112b7234613/tags/4d2bfad5-2e55-4059-ba43-783acb237772/edit');

        $this->assertResponseStatusCodeSame(403);
    }

    public function testInvalidUri(): void
    {
        $client = $this->login();
        $client->request('GET', '/app/events/aa-aa-aa-aa-aa/tags/aa-aa-aa-aa-aa-aa/edit');

        $this->assertResponseStatusCodeSame(404);
    }

    public function testTagNotFound(): void
    {
        $client = $this->login();
        $client->request('GET', '/app/events/f1f992d3-3cf5-4eb2-9b83-f112b7234613/tags/ecf8b44c-032d-404e-b9b1-0e9d5bc6086d/edit');

        $this->assertResponseStatusCodeSame(404);
    }

    public function testTagNotBelongsToEvent(): void
    {
        $client = $this->login();
        $client->request('GET', '/app/events/f1f992d3-3cf5-4eb2-9b83-f112b7234613/tags/9abf583d-8128-4da1-a359-736cfd3d13db/edit');

        $this->assertResponseStatusCodeSame(403);
    }

    public function testEventNotFound(): void
    {
        $client = $this->login();
        $client->request('GET', '/app/events/e2c992d3-3df5-0000-1234-f112b7234613/tags/4d2bfad5-2e55-4059-ba43-783acb237772/edit');

        $this->assertResponseStatusCodeSame(404);
    }

    public function testWithoutAuthenticatedUser(): void
    {
        $client = static::createClient();
        $client->request('GET', '/app/events/f1f992d3-3cf5-4eb2-9b83-f112b7234613/tags/4d2bfad5-2e55-4059-ba43-783acb237772/edit');
        $this->assertResponseRedirects('http://localhost/login', 302);
    }
}
