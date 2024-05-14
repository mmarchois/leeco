<?php

declare(strict_types=1);

namespace App\Tests\Integration\Infrastructure\Controller;

final class LoginControllerTest extends AbstractWebTestCase
{
    public function testLoginSuccessfully(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/login');

        $this->assertResponseStatusCodeSame(200);
        $this->assertSecurityHeaders();
        $this->assertSame('Se connecter', $crawler->filter('h1')->text());
        $this->assertMetaTitle('Se connecter - Leeco', $crawler);

        $saveButton = $crawler->selectButton('Se connecter');
        $form = $saveButton->form();
        $form['email'] = 'mathieu.marchois@gmail.com';
        $form['password'] = 'password123';
        $client->submit($form);

        $this->assertResponseStatusCodeSame(302);
        $client->followRedirect();
        $this->assertResponseStatusCodeSame(200);
        $this->assertRouteSame('app_dashboard');
    }

    public function testLoginWithNonVerifiedAccount(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/login');

        $this->assertResponseStatusCodeSame(200);

        $saveButton = $crawler->selectButton('Se connecter');
        $form = $saveButton->form();
        $form['email'] = 'helene.m.maitre@gmail.com';
        $form['password'] = 'password123';

        $client->submit($form);
        $this->assertResponseStatusCodeSame(302);
        $crawler = $client->followRedirect();

        $this->assertSame('Vous devez valider votre compte grâce au mail de confirmation reçu.', $crawler->filter('[data-testid="alert-danger"]')->text());
    }

    public function testLoginWithUnknownAccount(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/login');

        $this->assertResponseStatusCodeSame(200);

        $saveButton = $crawler->selectButton('Se connecter');
        $form = $saveButton->form();
        $form['email'] = 'bad.user@gmail.coop';
        $form['password'] = 'password';

        $client->submit($form);
        $this->assertResponseStatusCodeSame(302);
        $crawler = $client->followRedirect();

        $this->assertSame('Identifiants invalides.', $crawler->filter('[data-testid="alert-danger"]')->text());
    }

    public function testLoggedLogin(): void
    {
        $client = $this->login();
        $client->request('GET', '/login');

        $this->assertResponseStatusCodeSame(302);
        $client->followRedirect();
        $this->assertResponseStatusCodeSame(200);
        $this->assertRouteSame('app_dashboard');
    }
}
