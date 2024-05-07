<?php

declare(strict_types=1);

namespace App\Tests\Integration\Infrastructure\Controller\App\Event;

use App\Tests\Integration\Infrastructure\Controller\AbstractWebTestCase;

final class AddEventControllerTest extends AbstractWebTestCase
{
    public function testAdd(): void
    {
        $client = $this->login();
        $crawler = $client->request('GET', '/app/events/add');

        $this->assertResponseStatusCodeSame(200);
        $this->assertSecurityHeaders();
        $this->assertSame('Créer un évènement', $crawler->filter('h1')->text());
        $this->assertMetaTitle('Créer un évènement - Moment', $crawler);

        $this->assertBreadcrumbStructure([
            ['Mon espace', ['href' => '/app']],
            ['Mes évènements', ['href' => '/app/events']],
            ['Créer un évènement', ['href' => null]],
        ], $crawler);

        $saveButton = $crawler->selectButton('Sauvegarder');
        $form = $saveButton->form();
        $form['event_form[title]'] = 'Mariage cousins';
        $form['event_form[startDate]'] = '2024-10-14';
        $form['event_form[endDate]'] = '2024-10-14';
        $client->submit($form);
        $crawler = $client->followRedirect();

        $this->assertResponseStatusCodeSame(200);
        $this->assertRouteSame('app_events_list');
    }

    public function testInvalidData(): void
    {
        $client = $this->login();
        $crawler = $client->request('GET', '/app/events/add');

        $saveButton = $crawler->selectButton('Sauvegarder');
        $form = $saveButton->form();

        // Empty data
        $form['event_form[title]'] = '';
        $form['event_form[startDate]'] = '';
        $form['event_form[endDate]'] = '';
        $crawler = $client->submit($form);

        $this->assertResponseStatusCodeSame(422);
        $this->assertSame('Cette valeur ne doit pas être vide.', $crawler->filter('#event_form_title_error')->text());
        $this->assertSame('Cette valeur ne doit pas être vide.', $crawler->filter('#event_form_startDate_error')->text());
        $this->assertSame('Cette valeur ne doit pas être vide.', $crawler->filter('#event_form_endDate_error')->text());

        // Invalid data
        $form['event_form[title]'] = str_repeat('a', 101);
        $form['event_form[startDate]'] = 'abc';
        $form['event_form[endDate]'] = 'abc';
        $crawler = $client->submit($form);

        $this->assertResponseStatusCodeSame(422);
        $this->assertSame('Cette chaîne est trop longue. Elle doit avoir au maximum 100 caractères.', $crawler->filter('#event_form_title_error')->text());
        $this->assertSame('Veuillez entrer une date valide.', $crawler->filter('#event_form_startDate_error')->text());
        $this->assertSame('Veuillez entrer une date valide.', $crawler->filter('#event_form_endDate_error')->text());

        // Invalid period
        $form['event_form[title]'] = str_repeat('a', 101);
        $form['event_form[startDate]'] = '2024-10-14';
        $form['event_form[endDate]'] = '2024-10-10';
        $crawler = $client->submit($form);

        $this->assertResponseStatusCodeSame(422);
        $this->assertSame('La date de fin doit être supérieure à la date de début.', $crawler->filter('#event_form_endDate_error')->text());
    }

    public function testEventAlreadyExist(): void
    {
        $client = $this->login();
        $crawler = $client->request('GET', '/app/events/add');

        $saveButton = $crawler->selectButton('Sauvegarder');
        $form = $saveButton->form();

        $form['event_form[title]'] = 'Mariage H&M';
        $form['event_form[startDate]'] = '2035-10-25';
        $form['event_form[endDate]'] = '2035-10-25';
        $crawler = $client->submit($form);

        $this->assertResponseStatusCodeSame(422);
        $this->assertSame('Un évènement avec ce nom existe déjà.', $crawler->filter('#event_form_title_error')->text());
    }

    public function testWithoutAuthenticatedUser(): void
    {
        $client = static::createClient();
        $client->request('GET', '/app/events/add');
        $this->assertResponseRedirects('http://localhost/login', 302);
    }
}
