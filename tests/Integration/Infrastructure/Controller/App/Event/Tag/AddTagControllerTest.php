<?php

declare(strict_types=1);

namespace App\Tests\Integration\Infrastructure\Controller\App\Event\Tag;

use App\Tests\Integration\Infrastructure\Controller\AbstractWebTestCase;

final class AddTagControllerTest extends AbstractWebTestCase
{
    public function testAdd(): void
    {
        $client = $this->login();
        $crawler = $client->request('GET', '/app/events/f1f992d3-3cf5-4eb2-9b83-f112b7234613/tags/add');

        $this->assertResponseStatusCodeSame(200);
        $this->assertSecurityHeaders();
        $this->assertSame('Ajouter un tag', $crawler->filter('h1')->text());
        $this->assertMetaTitle('Ajouter un tag - Moment', $crawler);

        $this->assertBreadcrumbStructure([
            ['Mon espace', ['href' => '/app']],
            ['Mes évènements', ['href' => '/app/events']],
            ['Mariage H&M', ['href' => '/app/events/f1f992d3-3cf5-4eb2-9b83-f112b7234613']],
            ['Tags', ['href' => '/app/events/f1f992d3-3cf5-4eb2-9b83-f112b7234613/tags']],
            ['Ajouter un tag', ['href' => null]],
        ], $crawler);

        $saveButton = $crawler->selectButton('Sauvegarder');
        $form = $saveButton->form();
        $form['tag_form[title]'] = 'Scéance photo';
        $form['tag_form[startDate][date][day]'] = '5';
        $form['tag_form[startDate][date][month]'] = '1';
        $form['tag_form[startDate][date][year]'] = '2019';
        $form['tag_form[startDate][time][hour]'] = '18';
        $form['tag_form[startDate][time][minute]'] = '0';

        $form['tag_form[endDate][date][day]'] = '5';
        $form['tag_form[endDate][date][month]'] = '1';
        $form['tag_form[endDate][date][year]'] = '2019';
        $form['tag_form[endDate][time][hour]'] = '20';
        $form['tag_form[endDate][time][minute]'] = '0';
        $client->submit($form);
        $crawler = $client->followRedirect();
        $this->assertResponseStatusCodeSame(200);
        $this->assertRouteSame('app_tags_list');
        $this->assertSame(3, $crawler->filter('[data-testid="tag-list"] tbody tr')->count());
    }

    public function testBadValues(): void
    {
        $client = $this->login();
        $crawler = $client->request('GET', '/app/events/f1f992d3-3cf5-4eb2-9b83-f112b7234613/tags/add');

        $saveButton = $crawler->selectButton('Sauvegarder');

        // Empty values
        $form = $saveButton->form();
        $crawler = $client->submit($form);
        $this->assertResponseStatusCodeSame(422);

        $this->assertSame('Cette valeur ne doit pas être vide.', $crawler->filter('#tag_form_title_error')->text());

        // Bad values
        $form['tag_form[title]'] = str_repeat('a', 101);
        $form['tag_form[startDate][date][day]'] = '5';
        $form['tag_form[startDate][date][month]'] = '1';
        $form['tag_form[startDate][date][year]'] = '2019';
        $form['tag_form[startDate][time][hour]'] = '18';
        $form['tag_form[startDate][time][minute]'] = '0';

        $form['tag_form[endDate][date][day]'] = '5';
        $form['tag_form[endDate][date][month]'] = '1';
        $form['tag_form[endDate][date][year]'] = '2019';
        $form['tag_form[endDate][time][hour]'] = '10';
        $form['tag_form[endDate][time][minute]'] = '0';

        $crawler = $client->submit($form);
        $this->assertResponseStatusCodeSame(422);

        $this->assertSame('Cette chaîne est trop longue. Elle doit avoir au maximum 100 caractères.', $crawler->filter('#tag_form_title_error')->text());
        $this->assertSame('La date de fin doit être après le 05/01/2019.', $crawler->filter('#tag_form_endDate_error')->text());

        // Tag before event start
        $form['tag_form[startDate][date][year]'] = '2019';
        $form['tag_form[startDate][date][day]'] = '1';
        $form['tag_form[startDate][date][month]'] = '1';
        $crawler = $client->submit($form);
        $this->assertResponseStatusCodeSame(422);

        $this->assertSame("La date de début doit être après le 05/01/2019 (date de début de l'évènement).", $crawler->filter('#tag_form_startDate_error')->text());
    }

    public function testTagAlreadyExist(): void
    {
        $client = $this->login();
        $crawler = $client->request('GET', '/app/events/f1f992d3-3cf5-4eb2-9b83-f112b7234613/tags/add');

        $saveButton = $crawler->selectButton('Sauvegarder');
        $form = $saveButton->form();

        $form['tag_form[title]'] = 'Cérémonie religieuse';
        $form['tag_form[startDate][date][day]'] = '5';
        $form['tag_form[startDate][date][month]'] = '1';
        $form['tag_form[startDate][date][year]'] = '2019';
        $form['tag_form[startDate][time][hour]'] = '18';
        $form['tag_form[startDate][time][minute]'] = '0';
        $form['tag_form[endDate][date][day]'] = '5';
        $form['tag_form[endDate][date][month]'] = '1';
        $form['tag_form[endDate][date][year]'] = '2019';
        $form['tag_form[endDate][time][hour]'] = '20';
        $form['tag_form[endDate][time][minute]'] = '0';

        $crawler = $client->submit($form);

        $this->assertResponseStatusCodeSame(422);
        $this->assertSame('Ce tag existe déjà.', $crawler->filter('#tag_form_title_error')->text());
    }

    public function testAccessToAnEventNotOwned(): void
    {
        $client = $this->login('raphael.marchois@gmail.com');
        $client->request('GET', '/app/events/f1f992d3-3cf5-4eb2-9b83-f112b7234613/tags/add');

        $this->assertResponseStatusCodeSame(403);
    }

    public function testInvalidUri(): void
    {
        $client = $this->login();
        $client->request('GET', '/app/events/aa-aa-aa-aa-aa/tags/add');

        $this->assertResponseStatusCodeSame(404);
    }

    public function testEventNotFound(): void
    {
        $client = $this->login();
        $client->request('GET', '/app/events/e2c992d3-3df5-0000-1234-f112b7234613/tags/add');

        $this->assertResponseStatusCodeSame(404);
    }

    public function testWithoutAuthenticatedUser(): void
    {
        $client = static::createClient();
        $client->request('GET', '/app/events/f1f992d3-3cf5-4eb2-9b83-f112b7234613/tags/add');
        $this->assertResponseRedirects('http://localhost/login', 302);
    }
}
