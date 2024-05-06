<?php

declare(strict_types=1);

namespace App\Tests\Integration\Infrastructure\Controller\App\Profile;

use App\Tests\Integration\Infrastructure\Controller\AbstractWebTestCase;

final class EditProfileControllerTest extends AbstractWebTestCase
{
    public function testEditProfile(): void
    {
        $client = $this->login();
        $crawler = $client->request('GET', '/app/profile/edit');

        $this->assertResponseStatusCodeSame(200);
        $this->assertSecurityHeaders();
        $this->assertMetaTitle('Mon profil - Moment', $crawler);
        $this->assertBreadcrumbStructure([
            ['Mon espace', ['href' => '/app']],
            ['Mon profil', ['href' => null]],
        ], $crawler);

        $saveButton = $crawler->selectButton('Sauvegarder');
        $form = $saveButton->form();
        $form['profile_form[firstName]'] = 'Mathieu';
        $form['profile_form[lastName]'] = 'Marchois';
        $form['profile_form[email]'] = 'mathieu.marchois@gmail.com';
        $client->submit($form);

        $this->assertResponseStatusCodeSame(302);

        $crawler = $client->followRedirect();
        $this->assertSame('Votre profil a bien été mis à jour.', $crawler->filter('[data-testid="alert-success"]')->text());
    }

    public function testEmptyValues(): void
    {
        $client = $this->login();
        $crawler = $client->request('GET', '/app/profile/edit');

        $saveButton = $crawler->selectButton('Sauvegarder');
        $form = $saveButton->form();
        $form['profile_form[firstName]'] = '';
        $form['profile_form[lastName]'] = '';
        $form['profile_form[email]'] = '';
        $crawler = $client->submit($form);

        $this->assertResponseStatusCodeSame(422);
        $this->assertSame('Cette valeur ne doit pas être vide.', $crawler->filter('#profile_form_lastName_error')->text());
        $this->assertSame('Cette valeur ne doit pas être vide.', $crawler->filter('#profile_form_firstName_error')->text());
        $this->assertSame('Cette valeur ne doit pas être vide.', $crawler->filter('#profile_form_email_error')->text());
    }

    public function testBadValues(): void
    {
        $client = $this->login();
        $crawler = $client->request('GET', '/app/profile/edit');

        $saveButton = $crawler->selectButton('Sauvegarder');
        $form = $saveButton->form();
        $form['profile_form[firstName]'] = str_repeat('a', 101);
        $form['profile_form[lastName]'] = str_repeat('a', 101);
        $form['profile_form[email]'] = 'mathieu';

        $crawler = $client->submit($form);

        $this->assertResponseStatusCodeSame(422);
        $this->assertSame('Cette chaîne est trop longue. Elle doit avoir au maximum 100 caractères.', $crawler->filter('#profile_form_lastName_error')->text());
        $this->assertSame('Cette chaîne est trop longue. Elle doit avoir au maximum 100 caractères.', $crawler->filter('#profile_form_firstName_error')->text());
        $this->assertSame("Cette valeur n'est pas une adresse email valide.", $crawler->filter('#profile_form_email_error')->text());

        // Email too long
        $form['profile_form[email]'] = str_repeat('a', 101) . '@gmail.com';
        $crawler = $client->submit($form);
        $this->assertResponseStatusCodeSame(422);
        $this->assertSame('Cette chaîne est trop longue. Elle doit avoir au maximum 100 caractères.', $crawler->filter('#profile_form_email_error')->text());
    }

    public function testEmailAlreadyRegistered(): void
    {
        $client = $this->login();
        $crawler = $client->request('GET', '/app/profile/edit');

        $saveButton = $crawler->selectButton('Sauvegarder');
        $form = $saveButton->form();
        $form['profile_form[firstName]'] = 'Hélène';
        $form['profile_form[lastName]'] = 'Maitre';
        $form['profile_form[email]'] = 'helene.m.maitre@gmail.com';
        $crawler = $client->submit($form);

        $this->assertResponseStatusCodeSame(422);
        $this->assertSame('Un compte est déjà associé à cette adresse e-mail.', $crawler->filter('#profile_form_email_error')->text());
    }

    public function testWithoutAuthenticatedUser(): void
    {
        $client = static::createClient();
        $client->request('GET', '/app/profile/edit');

        $this->assertResponseRedirects('http://localhost/login', 302);
    }
}
