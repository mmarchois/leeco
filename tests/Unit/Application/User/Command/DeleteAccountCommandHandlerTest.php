<?php

declare(strict_types=1);

namespace App\Tests\Unit\Application\User\Command;

use App\Application\User\Command\DeleteAccountCommand;
use App\Application\User\Command\DeleteAccountCommandHandler;
use App\Domain\User\Exception\UserNotFoundException;
use App\Domain\User\Repository\UserRepositoryInterface;
use App\Domain\User\User;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

final class DeleteAccountCommandHandlerTest extends TestCase
{
    private MockObject $userRepository;

    public function setUp(): void
    {
        $this->userRepository = $this->createMock(UserRepositoryInterface::class);
    }

    public function testSuccessfullyRemoved(): void
    {
        $user = $this->createMock(User::class);
        $this->userRepository
            ->expects(self::once())
            ->method('findOneByUuid')
            ->with('0b507871-8b5e-4575-b297-a630310fc06e')
            ->willReturn($user);

        $this->userRepository
            ->expects(self::once())
            ->method('delete')
            ->with($user);

        $handler = new DeleteAccountCommandHandler($this->userRepository);
        $command = new DeleteAccountCommand('0b507871-8b5e-4575-b297-a630310fc06e');
        ($handler)($command);
    }

    public function testUserNotFound(): void
    {
        $this->expectException(UserNotFoundException::class);

        $this->userRepository
            ->expects(self::once())
            ->method('findOneByUuid')
            ->with('0b507871-8b5e-4575-b297-a630310fc06e')
            ->willReturn(null);

        $this->userRepository
            ->expects(self::never())
            ->method('delete');

        $handler = new DeleteAccountCommandHandler($this->userRepository);
        $command = new DeleteAccountCommand('0b507871-8b5e-4575-b297-a630310fc06e');
        ($handler)($command);
    }
}
