<?php

declare(strict_types=1);

namespace App\Tests\Integration\Infrastructure\Controller\Register;

use App\Tests\Integration\Infrastructure\Controller\AbstractWebTestCase;

final class RegisterControllerTest extends AbstractWebTestCase
{
    public function testRegisterSuccessfully(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/register');

        $this->assertResponseStatusCodeSame(200);
        $this->assertSecurityHeaders();
        $this->assertSame('Créer un compte', $crawler->filter('h1')->text());
        $this->assertMetaTitle('Créer un compte - Leeco', $crawler);

        $saveButton = $crawler->selectButton('Créer mon compte');
        $form = $saveButton->form();
        $form['register_user_form[firstName]'] = 'Hélène';
        $form['register_user_form[lastName]'] = 'Marchois';
        $form['register_user_form[email]'] = 'helene@gmail.com';
        $form['register_user_form[password][first]'] = 'password123';
        $form['register_user_form[password][second]'] = 'password123';
        $client->submit($form);

        $this->assertResponseStatusCodeSame(302);

        $this->assertEmailCount(1);
        $email = $this->getMailerMessage();
        $this->assertEmailHtmlBodyContains($email, 'Pour finaliser la création de votre compte, merci de cliquer sur le lien ci-dessous:');

        $client->followRedirect();
        $this->assertResponseStatusCodeSame(200);
        $this->assertRouteSame('app_register_succeeded');
    }

    public function testEmptyValues(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/register');

        $saveButton = $crawler->selectButton('Créer mon compte');
        $form = $saveButton->form();
        $crawler = $client->submit($form);

        $this->assertResponseStatusCodeSame(422);
        $this->assertSame('Cette valeur ne doit pas être vide.', $crawler->filter('#register_user_form_lastName_error')->text());
        $this->assertSame('Cette valeur ne doit pas être vide.', $crawler->filter('#register_user_form_firstName_error')->text());
        $this->assertSame('Cette valeur ne doit pas être vide.', $crawler->filter('#register_user_form_email_error')->text());
        $this->assertSame('Cette valeur ne doit pas être vide.', $crawler->filter('#register_user_form_password_first_error')->text());
    }

    public function testBadValues(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/register');

        $saveButton = $crawler->selectButton('Créer mon compte');
        $form = $saveButton->form();
        $form['register_user_form[firstName]'] = str_repeat('a', 101);
        $form['register_user_form[lastName]'] = str_repeat('a', 101);
        $form['register_user_form[email]'] = 'helene';
        $form['register_user_form[password][first]'] = 'password1234';
        $form['register_user_form[password][second]'] = 'password12345678910';

        $crawler = $client->submit($form);

        $this->assertResponseStatusCodeSame(422);
        $this->assertSame('Cette chaîne est trop longue. Elle doit avoir au maximum 100 caractères.', $crawler->filter('#register_user_form_firstName_error')->text());
        $this->assertSame('Cette chaîne est trop longue. Elle doit avoir au maximum 100 caractères.', $crawler->filter('#register_user_form_lastName_error')->text());
        $this->assertSame("Cette valeur n'est pas une adresse email valide.", $crawler->filter('#register_user_form_email_error')->text());
        $this->assertSame('Les valeurs ne correspondent pas.', $crawler->filter('#register_user_form_password_first_error')->text());

        // Password too short
        $form['register_user_form[password][first]'] = 'password';
        $form['register_user_form[password][second]'] = 'password';
        $crawler = $client->submit($form);
        $this->assertResponseStatusCodeSame(422);
        $this->assertSame('Cette chaîne est trop courte. Elle doit avoir au minimum 10 caractères.', $crawler->filter('#register_user_form_password_first_error')->text());

        // Email too long
        $form['register_user_form[email]'] = str_repeat('a', 101) . '@gmail.com';
        $crawler = $client->submit($form);
        $this->assertResponseStatusCodeSame(422);
        $this->assertSame('Cette chaîne est trop longue. Elle doit avoir au maximum 100 caractères.', $crawler->filter('#register_user_form_email_error')->text());
    }

    public function testEmailAlreadyRegistered(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/register');

        $saveButton = $crawler->selectButton('Créer mon compte');
        $form = $saveButton->form();
        $form['register_user_form[firstName]'] = 'Mathieu';
        $form['register_user_form[lastName]'] = 'Marchois';
        $form['register_user_form[email]'] = ' mathieu.marchois@gmail.com';
        $form['register_user_form[password][first]'] = 'password1234';
        $form['register_user_form[password][second]'] = 'password1234';

        $crawler = $client->submit($form);

        $this->assertResponseStatusCodeSame(422);
        $this->assertSame('Un compte est déjà associé à cette adresse e-mail.', $crawler->filter('#register_user_form_email_error')->text());
    }

    public function testLoggedRegister(): void
    {
        $client = $this->login();
        $client->request('GET', '/register');

        $this->assertResponseStatusCodeSame(302);
        $client->followRedirect();
        $this->assertResponseStatusCodeSame(200);
        $this->assertRouteSame('app_events_list');
    }
}
