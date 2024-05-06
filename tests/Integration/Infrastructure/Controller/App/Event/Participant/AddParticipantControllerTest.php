<?php

declare(strict_types=1);

namespace App\Tests\Integration\Infrastructure\Controller\App\Event\Participant;

use App\Tests\Integration\Infrastructure\Controller\AbstractWebTestCase;

final class AddParticipantControllerTest extends AbstractWebTestCase
{
    public function testAdd(): void
    {
        $client = $this->login();
        $crawler = $client->request('GET', '/app/events/f1f992d3-3cf5-4eb2-9b83-f112b7234613/participants/add');

        $this->assertResponseStatusCodeSame(200);
        $this->assertSecurityHeaders();
        $this->assertSame('Ajouter un participant', $crawler->filter('h1')->text());
        $this->assertMetaTitle('Ajouter un participant - Moment', $crawler);

        $this->assertBreadcrumbStructure([
            ['Mon espace', ['href' => '/app']],
            ['Mes évènements', ['href' => '/app/events']],
            ['Mariage H&M', ['href' => '/app/events/f1f992d3-3cf5-4eb2-9b83-f112b7234613']],
            ['Participants', ['href' => '/app/events/f1f992d3-3cf5-4eb2-9b83-f112b7234613/participants']],
            ['Ajouter un participant', ['href' => null]],
        ], $crawler);

        $saveButton = $crawler->selectButton('Sauvegarder');
        $form = $saveButton->form();
        $form['participant_form[firstName]'] = 'Hélène';
        $form['participant_form[lastName]'] = 'Marchois';
        $form['participant_form[email]'] = 'helene@gmail.com';
        $client->submit($form);

        $crawler = $client->followRedirect();
        $this->assertResponseStatusCodeSame(200);
        $this->assertRouteSame('app_participants_list');
        $this->assertSame(3, $crawler->filter('[data-testid="participant-list"] tbody tr')->count());
    }

    public function testBadValues(): void
    {
        $client = $this->login();
        $crawler = $client->request('GET', '/app/events/f1f992d3-3cf5-4eb2-9b83-f112b7234613/participants/add');

        $saveButton = $crawler->selectButton('Sauvegarder');

        // Empty values
        $form = $saveButton->form();
        $crawler = $client->submit($form);
        $this->assertResponseStatusCodeSame(422);

        $this->assertSame('Cette valeur ne doit pas être vide.', $crawler->filter('#participant_form_lastName_error')->text());
        $this->assertSame('Cette valeur ne doit pas être vide.', $crawler->filter('#participant_form_firstName_error')->text());
        $this->assertSame('Cette valeur ne doit pas être vide.', $crawler->filter('#participant_form_email_error')->text());

        // Bad values
        $form['participant_form[firstName]'] = str_repeat('a', 101);
        $form['participant_form[lastName]'] = str_repeat('a', 101);
        $form['participant_form[email]'] = 'helene';

        $crawler = $client->submit($form);
        $this->assertResponseStatusCodeSame(422);

        $this->assertSame('Cette chaîne est trop longue. Elle doit avoir au maximum 100 caractères.', $crawler->filter('#participant_form_firstName_error')->text());
        $this->assertSame('Cette chaîne est trop longue. Elle doit avoir au maximum 100 caractères.', $crawler->filter('#participant_form_lastName_error')->text());
        $this->assertSame("Cette valeur n'est pas une adresse email valide.", $crawler->filter('#participant_form_email_error')->text());

        // Email too long
        $form['participant_form[email]'] = str_repeat('a', 101) . '@gmail.com';

        $crawler = $client->submit($form);
        $this->assertResponseStatusCodeSame(422);
        $this->assertSame('Cette chaîne est trop longue. Elle doit avoir au maximum 100 caractères.', $crawler->filter('#participant_form_email_error')->text());
    }

    public function testParticipantAlreadyExist(): void
    {
        $client = $this->login();
        $crawler = $client->request('GET', '/app/events/f1f992d3-3cf5-4eb2-9b83-f112b7234613/participants/add');

        $saveButton = $crawler->selectButton('Sauvegarder');
        $form = $saveButton->form();

        $form['participant_form[firstName]'] = 'Tony & Corinne';
        $form['participant_form[lastName]'] = 'Marchois';
        $form['participant_form[email]'] = 'tc.marchois@gmail.com';
        $crawler = $client->submit($form);

        $this->assertResponseStatusCodeSame(422);
        $this->assertSame('Ce participant est déjà sur l\'évènement.', $crawler->filter('#participant_form_email_error')->text());
    }

    public function testAccessToAnEventNotOwned(): void
    {
        $client = $this->login('raphael.marchois@gmail.com');
        $client->request('GET', '/app/events/f1f992d3-3cf5-4eb2-9b83-f112b7234613/participants/add');

        $this->assertResponseStatusCodeSame(403);
    }

    public function testInvalidUri(): void
    {
        $client = $this->login();
        $client->request('GET', '/app/events/aa-aa-aa-aa-aa/participants/add');

        $this->assertResponseStatusCodeSame(404);
    }

    public function testEventNotFound(): void
    {
        $client = $this->login();
        $client->request('GET', '/app/events/e2c992d3-3df5-0000-1234-f112b7234613/participants/add');

        $this->assertResponseStatusCodeSame(404);
    }

    public function testWithoutAuthenticatedUser(): void
    {
        $client = static::createClient();
        $client->request('GET', '/app/events/f1f992d3-3cf5-4eb2-9b83-f112b7234613/participants/add');
        $this->assertResponseRedirects('http://localhost/login', 302);
    }
}
