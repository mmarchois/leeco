<?php

declare(strict_types=1);

namespace App\Tests\Integration\Infrastructure\Controller\App\Event\Participant;

use App\Tests\Integration\Infrastructure\Controller\AbstractWebTestCase;

final class EditParticipantControllerTest extends AbstractWebTestCase
{
    public function testEdit(): void
    {
        $client = $this->login();
        $crawler = $client->request('GET', '/app/events/f1f992d3-3cf5-4eb2-9b83-f112b7234613/participants/0faf6d38-6887-44b9-9896-7877e31c56c4/edit');

        $this->assertResponseStatusCodeSame(200);
        $this->assertSecurityHeaders();
        $this->assertSame('Modifier le participant "Tony & Corinne MARCHOIS"', $crawler->filter('h1')->text());
        $this->assertMetaTitle('Modifier le participant "Tony & Corinne MARCHOIS" - Moment', $crawler);

        $saveButton = $crawler->selectButton('Sauvegarder');
        $form = $saveButton->form();
        $form['participant_form[firstName]'] = 'Tony Ou Corinne';
        $form['participant_form[lastName]'] = 'Marchois';
        $form['participant_form[email]'] = 'tc.marchois@gmail.com';
        $client->submit($form);

        $crawler = $client->followRedirect();
        $this->assertResponseStatusCodeSame(200);
        $this->assertRouteSame('app_participants_list');
    }

    public function testBadValues(): void
    {
        $client = $this->login();
        $crawler = $client->request('GET', '/app/events/f1f992d3-3cf5-4eb2-9b83-f112b7234613/participants/0faf6d38-6887-44b9-9896-7877e31c56c4/edit');

        $saveButton = $crawler->selectButton('Sauvegarder');

        // Empty values
        $form = $saveButton->form();
        $form['participant_form[firstName]'] = '';
        $form['participant_form[lastName]'] = '';
        $form['participant_form[email]'] = '';
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

    public function testEditParticipantThatAlreadyExist(): void
    {
        $client = $this->login();
        $crawler = $client->request('GET', '/app/events/f1f992d3-3cf5-4eb2-9b83-f112b7234613/participants/0faf6d38-6887-44b9-9896-7877e31c56c4/edit');

        $saveButton = $crawler->selectButton('Sauvegarder');
        $form = $saveButton->form();

        $form['participant_form[firstName]'] = 'Floran';
        $form['participant_form[lastName]'] = 'Roisin';
        $form['participant_form[email]'] = 'floran.roisin@gmail.com';
        $crawler = $client->submit($form);

        $this->assertResponseStatusCodeSame(422);
        $this->assertSame('Ce participant est déjà sur l\'évènement.', $crawler->filter('#participant_form_email_error')->text());
    }

    public function testParticipantNotBelongsToEvent(): void
    {
        $client = $this->login();
        $client->request('GET', '/app/events/f1f992d3-3cf5-4eb2-9b83-f112b7234613/participants/e4095f02-1516-42b3-82d1-506f2e74f027/edit');

        $this->assertResponseStatusCodeSame(403);
    }

    public function testParticpantNotFound(): void
    {
        $client = $this->login();
        $client->request('GET', '/app/events/f1f992d3-3cf5-4eb2-9b83-f112b7234613/participants/3ccb2b35-04a5-45d2-bcf5-2dcb1eda76dc/edit');

        $this->assertResponseStatusCodeSame(404);
    }

    public function testEventNotBelongsToUser(): void
    {
        $client = $this->login('raphael.marchois@gmail.com');
        $client->request('GET', '/app/events/f1f992d3-3cf5-4eb2-9b83-f112b7234613/participants/0faf6d38-6887-44b9-9896-7877e31c56c4/edit');

        $this->assertResponseStatusCodeSame(403);
    }

    public function testEventNotFound(): void
    {
        $client = $this->login();
        $client->request('GET', '/app/events/e2c992d3-3df5-0000-1234-f112b7234613/participants/0faf6d38-6887-44b9-9896-7877e31c56c4/edit');

        $this->assertResponseStatusCodeSame(404);
    }

    public function testInvalidUri(): void
    {
        $client = $this->login();
        $client->request('GET', '/events/aa-aa-aa-aa-aa/participants/aa-aa-aa-aa-aa/edit');

        $this->assertResponseStatusCodeSame(404);
    }

    public function testWithoutAuthenticatedUser(): void
    {
        $client = static::createClient();
        $client->request('GET', '/app/events/f1f992d3-3cf5-4eb2-9b83-f112b7234613/participants/0faf6d38-6887-44b9-9896-7877e31c56c4/edit');
        $this->assertResponseRedirects('http://localhost/login', 302);
    }
}
