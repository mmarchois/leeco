<?php

declare(strict_types=1);

namespace App\Tests\Integration\Infrastructure\Controller\App\Profile;

use App\Tests\Integration\Infrastructure\Controller\AbstractWebTestCase;
use App\Tests\SessionHelper;

final class DeleteAccountControllerTest extends AbstractWebTestCase
{
    use SessionHelper;

    public function testDelete(): void
    {
        $client = $this->login();
        $client->request('DELETE', '/app/profile/delete', [
            'token' => $this->generateCsrfToken($client, 'delete-user-account'),
        ]);
        $this->assertResponseRedirects('/', 303);
    }

    public function testInvalidCsrfToken(): void
    {
        $client = $this->login();
        $client->request('DELETE', '/app/profile/delete');
        $this->assertResponseStatusCodeSame(400);
    }

    public function testWithoutAuthentication(): void
    {
        $client = static::createClient();
        $client->request('DELETE', '/app/profile/delete', [
            'token' => $this->generateCsrfToken($client, 'delete-user-account'),
        ]);
        $this->assertResponseRedirects('http://localhost/login', 302);
    }
}
