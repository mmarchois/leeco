<?php

declare(strict_types=1);

namespace App\Tests\Unit\Application\Guest\Command;

use App\Application\Guest\Command\DeleteGuestCommand;
use App\Application\Guest\Command\DeleteGuestCommandHandler;
use App\Domain\Event\Event;
use App\Domain\Guest\Exception\GuestNotBelongsToEventException;
use App\Domain\Guest\Exception\GuestNotFoundException;
use App\Domain\Guest\Guest;
use App\Domain\Guest\Repository\GuestRepositoryInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

final class DeleteGuestCommandHandlerTest extends TestCase
{
    private MockObject $guestRepository;
    private MockObject $event;
    private DeleteGuestCommand $command;

    public function setUp(): void
    {
        $this->guestRepository = $this->createMock(GuestRepositoryInterface::class);
        $this->event = $this->createMock(Event::class);

        $command = new DeleteGuestCommand($this->event, 'f9c69d5b-6e68-44f0-86f2-a7e93136410c');
        $this->command = $command;
    }

    public function testDelete(): void
    {
        $guest = $this->createMock(Guest::class);

        $this->guestRepository
            ->expects(self::once())
            ->method('findOneByUuid')
            ->with('f9c69d5b-6e68-44f0-86f2-a7e93136410c')
            ->willReturn($guest);

        $guest
            ->expects(self::once())
            ->method('getEvent')
            ->willReturn($this->event);

        $handler = new DeleteGuestCommandHandler($this->guestRepository);

        $this->assertEquals('f9c69d5b-6e68-44f0-86f2-a7e93136410c', ($handler)($this->command));
    }

    public function testDeleteGuestNotBelongsToEvent(): void
    {
        $this->expectException(GuestNotBelongsToEventException::class);

        $guest = $this->createMock(Guest::class);
        $event = $this->createMock(Event::class);

        $this->guestRepository
            ->expects(self::once())
            ->method('findOneByUuid')
            ->with('f9c69d5b-6e68-44f0-86f2-a7e93136410c')
            ->willReturn($guest);

        $guest
            ->expects(self::once())
            ->method('getEvent')
            ->willReturn($event);

        $handler = new DeleteGuestCommandHandler($this->guestRepository);

        ($handler)($this->command);
    }

    public function testGuestNotFound(): void
    {
        $this->expectException(GuestNotFoundException::class);

        $this->guestRepository
            ->expects(self::once())
            ->method('findOneByUuid')
            ->with('f9c69d5b-6e68-44f0-86f2-a7e93136410c')
            ->willReturn(null);

        $this->guestRepository
            ->expects(self::never())
            ->method('delete');

        $handler = new DeleteGuestCommandHandler($this->guestRepository);

        ($handler)($this->command);
    }
}
