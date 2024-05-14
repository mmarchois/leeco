<?php

declare(strict_types=1);

namespace App\Tests\Integration\Infrastructure\Controller;

final class ForgotPasswordControllerTest extends AbstractWebTestCase
{
    public function testForgotPassword(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/forgot-password');

        $this->assertResponseStatusCodeSame(200);
        $this->assertSecurityHeaders();
        $this->assertSame('Mot de passe oublié ?', $crawler->filter('h1')->text());
        $this->assertMetaTitle('Mot de passe oublié ? - Leeco', $crawler);

        $saveButton = $crawler->selectButton('Envoyer un lien de réinitialisation');
        $form = $saveButton->form();
        $form['forgot_password_form[email]'] = 'mathieu.marchois@gmail.com';
        $client->submit($form);

        $this->assertResponseStatusCodeSame(302);

        $this->assertEmailCount(1);
        $email = $this->getMailerMessage();
        $this->assertEmailHtmlBodyContains($email, 'Vous avez demandé la réinitialisation du mot de passe associé à votre compte. Pour le modifier, cliquez ci-dessous');

        $crawler = $client->followRedirect();
        $this->assertResponseStatusCodeSame(200);
        $this->assertSame('Consultez votre boîte mail pour trouver un lien vous permettant de réinitialiser votre mot de passe.', $crawler->filter('[data-testid="alert-success"]')->text());
        $this->assertRouteSame('app_forgot_password');
    }

    public function testEmptyValues(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/forgot-password');

        $saveButton = $crawler->selectButton('Envoyer un lien de réinitialisation');
        $form = $saveButton->form();
        $crawler = $client->submit($form);

        $this->assertResponseStatusCodeSame(422);
        $this->assertSame('Cette valeur ne doit pas être vide.', $crawler->filter('#forgot_password_form_email_error')->text());
    }

    public function testBadValues(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/forgot-password');

        $saveButton = $crawler->selectButton('Envoyer un lien de réinitialisation');
        $form = $saveButton->form();
        $form['forgot_password_form[email]'] = 'mathieu';

        $crawler = $client->submit($form);

        $this->assertResponseStatusCodeSame(422);
        $this->assertSame("Cette valeur n'est pas une adresse email valide.", $crawler->filter('#forgot_password_form_email_error')->text());

        // Email too long
        $form['forgot_password_form[email]'] = str_repeat('a', 101) . '@gmail.com';
        $crawler = $client->submit($form);
        $this->assertResponseStatusCodeSame(422);
        $this->assertSame('Cette chaîne est trop longue. Elle doit avoir au maximum 100 caractères.', $crawler->filter('#forgot_password_form_email_error')->text());
    }
}
