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
        $this->assertSame('Ajouter un évènement', $crawler->filter('h1')->text());
        $this->assertMetaTitle('Ajouter un évènement - Moment', $crawler);

        $saveButton = $crawler->selectButton('Sauvegarder');
        $form = $saveButton->form();
        $form['event_form[title]'] = 'Mariage cousins';
        $form['event_form[date]'] = '2023-02-14';
        $client->submit($form);
        $crawler = $client->followRedirect();

        $this->assertResponseStatusCodeSame(200);
        $this->assertRouteSame('app_events_dashboard');
        $this->assertSame('Mariage cousins', $crawler->filter('h1')->text());
    }

    public function testInvalidData(): void
    {
        $client = $this->login();
        $crawler = $client->request('GET', '/app/events/add');

        $saveButton = $crawler->selectButton('Sauvegarder');
        $form = $saveButton->form();
        // Empty data
        $form['event_form[title]'] = '';
        $form['event_form[date]'] = '';
        $crawler = $client->submit($form);

        $this->assertResponseStatusCodeSame(422);
        $this->assertSame('Cette valeur ne doit pas être vide.', $crawler->filter('#event_form_title_error')->text());
        $this->assertSame('Cette valeur ne doit pas être vide.', $crawler->filter('#event_form_date_error')->text());

        // Invalid data
        $form['event_form[title]'] = str_repeat('a', 101);
        $form['event_form[date]'] = 'abc';
        $crawler = $client->submit($form);

        $this->assertResponseStatusCodeSame(422);
        $this->assertSame('Cette chaîne est trop longue. Elle doit avoir au maximum 100 caractères.', $crawler->filter('#event_form_title_error')->text());
        $this->assertSame('Veuillez entrer une date valide.', $crawler->filter('#event_form_date_error')->text());
    }

    public function testEventAlreadyExist(): void
    {
        $client = $this->login();
        $crawler = $client->request('GET', '/app/events/add');

        $saveButton = $crawler->selectButton('Sauvegarder');
        $form = $saveButton->form();

        $form['event_form[title]'] = 'Mariage H&M';
        $form['event_form[date]'] = '2023-01-01';
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
