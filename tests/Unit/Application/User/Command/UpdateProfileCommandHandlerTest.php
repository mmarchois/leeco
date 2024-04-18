<?php

declare(strict_types=1);

namespace App\Tests\Unit\Application\User\Command;

use App\Application\User\Command\UpdateProfileCommand;
use App\Application\User\Command\UpdateProfileCommandHandler;
use App\Domain\User\Exception\UserAlreadyRegisteredException;
use App\Domain\User\Specification\IsUserAlreadyRegistered;
use App\Domain\User\User;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

final class UpdateProfileCommandHandlerTest extends TestCase
{
    private MockObject $isUserAlreadyRegistered;
    private MockObject $user;
    private UpdateProfileCommand $command;

    public function setUp(): void
    {
        $this->isUserAlreadyRegistered = $this->createMock(IsUserAlreadyRegistered::class);
        $this->user = $this->createMock(User::class);

        $command = new UpdateProfileCommand($this->user);
        $command->firstName = 'Mathieu';
        $command->lastName = 'MARCHOIS';
        $command->email = '  mathIeu.mArchois@gmail.com   '; // Voluntary add spaces

        $this->command = $command;
    }

    public function testProfileUpdated(): void
    {
        $this->user
            ->expects(self::once())
            ->method('getEmail')
            ->willReturn('mathieu.marchois@gmail.com');

        $this->user
             ->expects(self::once())
             ->method('update')
             ->with(
                 'Mathieu',
                 'MARCHOIS',
                 'mathieu.marchois@gmail.com',
             );

        $this->isUserAlreadyRegistered
            ->expects(self::never())
            ->method('isSatisfiedBy');

        $handler = new UpdateProfileCommandHandler($this->isUserAlreadyRegistered);
        $this->assertEquals(($handler)($this->command), $this->user);
    }

    public function testUserAlreadyExisted(): void
    {
        $this->expectException(UserAlreadyRegisteredException::class);

        $this->command->email = 'helene@fairness.coop';

        $this->user
            ->expects(self::once())
            ->method('getEmail')
            ->willReturn('mathieu.marchois@gmail.com');

        $this->user
            ->expects(self::never())
            ->method('update');

        $this->isUserAlreadyRegistered
            ->expects(self::once())
            ->method('isSatisfiedBy')
            ->with('helene@fairness.coop')
            ->willReturn(true);

        $handler = new UpdateProfileCommandHandler($this->isUserAlreadyRegistered);
        ($handler)($this->command);
    }
}
