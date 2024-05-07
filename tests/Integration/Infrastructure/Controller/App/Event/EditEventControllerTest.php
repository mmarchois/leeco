<?php

declare(strict_types=1);

namespace App\Tests\Integration\Infrastructure\Controller\App\Event;

use App\Tests\Integration\Infrastructure\Controller\AbstractWebTestCase;

final class EditEventControllerTest extends AbstractWebTestCase
{
    public function testEdit(): void
    {
        $client = $this->login();
        $crawler = $client->request('GET', '/app/events/f1f992d3-3cf5-4eb2-9b83-f112b7234613/edit');

        $this->assertResponseStatusCodeSame(200);
        $this->assertSecurityHeaders();
        $this->assertSame('Modifier "Mariage H&M"', $crawler->filter('h1')->text());
        $this->assertMetaTitle('Modifier "Mariage H&M" - Moment', $crawler);

        $this->assertBreadcrumbStructure([
            ['Mon espace', ['href' => '/app']],
            ['Mes évènements', ['href' => '/app/events']],
            ['Modifier "Mariage H&M"', ['href' => null]],
        ], $crawler);

        $saveButton = $crawler->selectButton('Sauvegarder');
        $form = $saveButton->form();
        $form['event_form[title]'] = 'Mariage H&M2';
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
        $crawler = $client->request('GET', '/app/events/f1f992d3-3cf5-4eb2-9b83-f112b7234613/edit');

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
        $crawler = $client->request('GET', '/app/events/f1f992d3-3cf5-4eb2-9b83-f112b7234613/edit');

        $saveButton = $crawler->selectButton('Sauvegarder');
        $form = $saveButton->form();

        $form['event_form[title]'] = 'EVG Julien';
        $form['event_form[startDate]'] = '2024-10-14';
        $form['event_form[endDate]'] = '2024-10-16';
        $crawler = $client->submit($form);

        $this->assertResponseStatusCodeSame(422);
        $this->assertSame('Un évènement avec ce nom existe déjà.', $crawler->filter('#event_form_title_error')->text());
    }

    public function testEditAnEventNotOwned(): void
    {
        $client = $this->login('raphael.marchois@gmail.com');
        $client->request('GET', '/app/events/f1f992d3-3cf5-4eb2-9b83-f112b7234613/edit');

        $this->assertResponseStatusCodeSame(403);
    }

    public function testEditEventNotFound(): void
    {
        $client = $this->login();
        $client->request('GET', '/app/events/1d288130-7317-42b6-b2a7-7fd6cd0918de/edit');

        $this->assertResponseStatusCodeSame(404);
    }

    public function testEditInvalidUri(): void
    {
        $client = $this->login();
        $client->request('GET', '/app/events/aa-aa-aa-aa-aa/edit');

        $this->assertResponseStatusCodeSame(404);
    }

    public function testWithoutAuthenticatedUser(): void
    {
        $client = static::createClient();
        $client->request('GET', '/app/events/f1f992d3-3cf5-4eb2-9b83-f112b7234613/edit');
        $this->assertResponseRedirects('http://localhost/login', 302);
    }
}
