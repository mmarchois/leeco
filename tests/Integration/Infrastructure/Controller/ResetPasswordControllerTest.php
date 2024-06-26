<?php

declare(strict_types=1);

namespace App\Tests\Integration\Infrastructure\Controller;

final class ResetPasswordControllerTest extends AbstractWebTestCase
{
    public function testResetPasswordSuccessfully(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/reset-password/forgotPasswordToken');

        $this->assertResponseStatusCodeSame(200);
        $this->assertSecurityHeaders();
        $this->assertSame('Changer mon mot de passe', $crawler->filter('h1')->text());
        $this->assertMetaTitle('Changer mon mot de passe - Leeco', $crawler);

        $saveButton = $crawler->selectButton('Changer mon mot de passe');
        $form = $saveButton->form();
        $form['reset_password_form[password][first]'] = 'password123';
        $form['reset_password_form[password][second]'] = 'password123';
        $client->submit($form);

        $this->assertResponseStatusCodeSame(302);

        $crawler = $client->followRedirect();
        $this->assertResponseStatusCodeSame(200);
        $this->assertRouteSame('app_login');
        $this->assertSame('Votre mot de passe a bien été changé. Vous pouvez dès à présent vous connecter en utilisant votre nouveau mot de passe.', $crawler->filter('[data-testid="alert-success"]')->text());
    }

    public function testEmptyValues(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/reset-password/forgotPasswordToken');

        $saveButton = $crawler->selectButton('Changer mon mot de passe');
        $form = $saveButton->form();
        $crawler = $client->submit($form);

        $this->assertResponseStatusCodeSame(422);
        $this->assertSame('Cette valeur ne doit pas être vide.', $crawler->filter('#reset_password_form_password_first_error')->text());
    }

    public function testBadValues(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/reset-password/forgotPasswordToken');

        $saveButton = $crawler->selectButton('Changer mon mot de passe');
        $form = $saveButton->form();
        $form['reset_password_form[password][first]'] = 'password1234';
        $form['reset_password_form[password][second]'] = 'password12345678910';

        $crawler = $client->submit($form);

        $this->assertResponseStatusCodeSame(422);
        $this->assertSame('Les valeurs ne correspondent pas.', $crawler->filter('#reset_password_form_password_first_error')->text());

        // Password too short
        $form['reset_password_form[password][first]'] = 'password';
        $form['reset_password_form[password][second]'] = 'password';
        $crawler = $client->submit($form);
        $this->assertResponseStatusCodeSame(422);
        $this->assertSame('Cette chaîne est trop courte. Elle doit avoir au minimum 10 caractères.', $crawler->filter('#reset_password_form_password_first_error')->text());
    }

    public function testTokenNotFound(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/reset-password/tokenNotFound');

        $saveButton = $crawler->selectButton('Changer mon mot de passe');
        $form = $saveButton->form();
        $form['reset_password_form[password][first]'] = 'password123';
        $form['reset_password_form[password][second]'] = 'password123';
        $client->submit($form);

        $this->assertResponseStatusCodeSame(302);

        $crawler = $client->followRedirect();
        $this->assertSame('Le changement de mot de passe a échoué, veuillez faire une nouvelle demande.', $crawler->filter('[data-testid="alert-danger"]')->text());
    }

    public function testTokenExpired(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/reset-password/expiredForgotPasswordToken');

        $saveButton = $crawler->selectButton('Changer mon mot de passe');
        $form = $saveButton->form();
        $form['reset_password_form[password][first]'] = 'password123';
        $form['reset_password_form[password][second]'] = 'password123';
        $client->submit($form);

        $this->assertResponseStatusCodeSame(302);

        $crawler = $client->followRedirect();
        $this->assertSame('Le changement de mot de passe a échoué, veuillez faire une nouvelle demande.', $crawler->filter('[data-testid="alert-danger"]')->text());
    }
}
