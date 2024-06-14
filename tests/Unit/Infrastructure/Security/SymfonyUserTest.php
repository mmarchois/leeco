<?php

declare(strict_types=1);

namespace App\Tests\Unit\Infrastructure\Security;

use App\Domain\User\User;
use App\Infrastructure\Security\SymfonyUser;
use PHPUnit\Framework\TestCase;

final class SymfonyUserTest extends TestCase
{
    public function testUser()
    {
        $user = $this->createMock(User::class);
        $user
            ->expects(self::once())
            ->method('getUuid')
            ->willReturn('2d3724f1-2910-48b4-ba56-81796f6e100b');
        $user
            ->expects(self::once())
            ->method('getEmail')
            ->willReturn('mathieu.marchois@gmail.com');
        $user
            ->expects(self::once())
            ->method('getFirstName')
            ->willReturn('Mathieu');
        $user
            ->expects(self::once())
            ->method('getLastName')
            ->willReturn('MARCHOIS');
        $user
            ->expects(self::once())
            ->method('getPassword')
            ->willReturn('password');
        $user
            ->expects(self::once())
            ->method('isVerified')
            ->willReturn(false);

        $symfonyUser = new SymfonyUser($user);

        $this->assertSame('2d3724f1-2910-48b4-ba56-81796f6e100b', $symfonyUser->getUuid());
        $this->assertSame(['ROLE_USER'], $symfonyUser->getRoles());
        $this->assertSame('Mathieu', $symfonyUser->getFirstName());
        $this->assertSame('MARCHOIS', $symfonyUser->getLastName());
        $this->assertSame(null, $symfonyUser->getSalt());
        $this->assertSame('mathieu.marchois@gmail.com', $symfonyUser->getUsername());
        $this->assertSame('mathieu.marchois@gmail.com', $symfonyUser->getUserIdentifier());
        $this->assertSame('password', $symfonyUser->getPassword());
        $this->assertEmpty($symfonyUser->eraseCredentials());
        $this->assertFalse($symfonyUser->isVerified());

        $user2 = $this->createMock(User::class);
        $user2
            ->expects(self::once())
            ->method('getEmail')
            ->willReturn('mathieu@gmail.com');
        $user2
            ->expects(self::once())
            ->method('getFirstName')
            ->willReturn('Mathieuu');
        $user2
            ->expects(self::once())
            ->method('getLastName')
            ->willReturn('MARCHOISS');

        $symfonyUser->update($user2);
        $this->assertSame('Mathieuu', $symfonyUser->getFirstName());
        $this->assertSame('MARCHOISS', $symfonyUser->getLastName());
        $this->assertSame('mathieu@gmail.com', $symfonyUser->getUsername());
        $this->assertSame('mathieu@gmail.com', $symfonyUser->getUserIdentifier());
    }
}
