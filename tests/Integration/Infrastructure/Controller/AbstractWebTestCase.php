<?php

declare(strict_types=1);

namespace App\Tests\Integration\Infrastructure\Controller;

use App\Infrastructure\Persistence\Doctrine\Repository\User\UserRepository;
use App\Infrastructure\Security\SymfonyUser;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\DomCrawler\Crawler;

abstract class AbstractWebTestCase extends WebTestCase
{
    protected function login(string $email = 'mathieu.marchois@gmail.com'): KernelBrowser
    {
        $client = static::createClient();
        $userRepository = static::getContainer()->get(UserRepository::class);
        $testUser = $userRepository->findOneByEmail($email);

        $client->loginUser(new SymfonyUser($testUser));

        return $client;
    }

    protected function assertMetaTitle(string $title, Crawler $crawler): void
    {
        $this->assertEquals($title, $crawler->filter('title')->text());
    }

    protected function assertSecurityHeaders(): void
    {
        $this->assertResponseHeaderSame('X-XSS-Protection', '1; mode=block');
        $this->assertResponseHeaderSame('X-Frame-Options', 'DENY');
        $this->assertResponseHeaderSame('X-Content-Type-Options', 'nosniff');
        // $this->assertResponseHasHeader('X-Content-Security-Policy');
        // $this->assertResponseHasHeader('Content-Security-Policy');
    }

    protected function assertNavStructure(array $expectedStructure, Crawler $crawler): void
    {
        $actualStructure = $crawler
            ->filter('header nav a')
            ->each(function (Crawler $node, int $i): array {
                return [$node->text(), ['href' => $node->attr('href')]];
            });

        $this->assertSame(\count($expectedStructure), \count($actualStructure));

        foreach ($expectedStructure as $index => [$text, $attrs]) {
            [$actualText, $actualAttrs] = $actualStructure[$index];
            $this->assertSame($text, $actualText);
            $this->assertEmpty(array_diff($attrs, $actualAttrs));
        }
    }
}
