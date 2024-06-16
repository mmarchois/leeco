<?php

declare(strict_types=1);

namespace App\Tests\Integration\Infrastructure\Controller\Api\Guest;

use App\Tests\Integration\Infrastructure\Controller\AbstractWebTestCase;

final class RegisterControllerTest extends AbstractWebTestCase
{
    public function testRegister(): void
    {
        static::createClient()->jsonRequest('POST', '/api/guests/register', [
            'firstName' => 'Mathieu',
            'lastName' => 'MARCHOIS',
            'deviceIdentifier' => '123456789',
            'eventUuid' => 'f1f992d3-3cf5-4eb2-9b83-f112b7234613',
        ]);

        $this->assertResponseStatusCodeSame(201);
        $this->assertResponseHeaderSame('content-type', 'application/json');
    }

    public function testGuestAlreadyExist(): void
    {
        $client = static::createClient();
        $client->jsonRequest('POST', '/api/guests/register', [
            'firstName' => 'Tony',
            'lastName' => 'MARCHOIS',
            'deviceIdentifier' => '9C287922-EE26-4501-94B5-DDE6F83E1475',
            'eventUuid' => 'f1f992d3-3cf5-4eb2-9b83-f112b7234613',
        ]);

        $this->assertResponseStatusCodeSame(400);
        $this->assertResponseHeaderSame('content-type', 'application/json');
        $content = json_decode($client->getResponse()->getContent(), true);
        $this->assertSame($content['detail'], 'Vous êtes déjà inscrit sur l\'évènement.');
    }

    public function testEventNotFound(): void
    {
        $client = static::createClient();
        $client->jsonRequest('POST', '/api/guests/register', [
            'firstName' => 'Tony',
            'lastName' => 'MARCHOIS',
            'deviceIdentifier' => '9C287922-EE26-4501-94B5-DDE6F83E1475',
            'eventUuid' => 'cebe5304-1666-4038-befb-b6d0528a9bea',
        ]);

        $this->assertResponseStatusCodeSame(400);
        $this->assertResponseHeaderSame('content-type', 'application/json');
        $content = json_decode($client->getResponse()->getContent(), true);
        $this->assertSame($content['detail'], 'Cet évènement n\'existe pas.');
    }

    public function testEmptyValues(): void
    {
        $client = static::createClient();
        $client->jsonRequest('POST', '/api/guests/register', [
            'firstName' => '',
            'lastName' => '',
            'deviceIdentifier' => '',
            'eventUuid' => '',
        ]);
        $content = json_decode($client->getResponse()->getContent(), true);
        $violations = $content['violations'];

        $this->assertResponseStatusCodeSame(422);
        $this->assertResponseHeaderSame('content-type', 'application/json');
        $this->assertSame($violations[0]['propertyPath'], 'firstName');
        $this->assertSame($violations[0]['title'], 'Cette valeur ne doit pas être vide.');

        $this->assertSame($violations[1]['propertyPath'], 'lastName');
        $this->assertSame($violations[1]['title'], 'Cette valeur ne doit pas être vide.');

        $this->assertSame($violations[2]['propertyPath'], 'deviceIdentifier');
        $this->assertSame($violations[2]['title'], 'Cette valeur ne doit pas être vide.');

        $this->assertSame($violations[3]['propertyPath'], 'eventUuid');
        $this->assertSame($violations[3]['title'], 'Cette valeur ne doit pas être vide.');
    }

    public function testBadValues(): void
    {
        $client = static::createClient();
        $client->jsonRequest('POST', '/api/guests/register', [
            'firstName' => str_repeat('a', 101),
            'lastName' => str_repeat('a', 101),
            'deviceIdentifier' => str_repeat('a', 51),
            'eventUuid' => 'abc',
        ]);
        $content = json_decode($client->getResponse()->getContent(), true);
        $violations = $content['violations'];

        $this->assertResponseStatusCodeSame(422);
        $this->assertResponseHeaderSame('content-type', 'application/json');

        $this->assertSame($violations[0]['propertyPath'], 'firstName');
        $this->assertSame($violations[0]['title'], 'Cette chaîne est trop longue. Elle doit avoir au maximum 100 caractères.');

        $this->assertSame($violations[1]['propertyPath'], 'lastName');
        $this->assertSame($violations[1]['title'], 'Cette chaîne est trop longue. Elle doit avoir au maximum 100 caractères.');

        $this->assertSame($violations[2]['propertyPath'], 'deviceIdentifier');
        $this->assertSame($violations[2]['title'], 'Cette chaîne est trop longue. Elle doit avoir au maximum 50 caractères.');

        $this->assertSame($violations[3]['propertyPath'], 'eventUuid');
        $this->assertSame($violations[3]['title'], 'Cette valeur n\'est pas un UUID valide.');
    }
}
