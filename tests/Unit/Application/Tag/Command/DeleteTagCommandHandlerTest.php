<?php

declare(strict_types=1);

namespace App\Tests\Unit\Application\Tag\Command;

use App\Application\Tag\Command\DeleteTagCommand;
use App\Application\Tag\Command\DeleteTagCommandHandler;
use App\Domain\Event\Event;
use App\Domain\Tag\Exception\TagNotBelongsToEventException;
use App\Domain\Tag\Exception\TagNotFoundException;
use App\Domain\Tag\Repository\TagRepositoryInterface;
use App\Domain\Tag\Tag;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

final class DeleteTagCommandHandlerTest extends TestCase
{
    private MockObject $tagRepository;
    private MockObject $event;
    private DeleteTagCommand $command;

    public function setUp(): void
    {
        $this->tagRepository = $this->createMock(TagRepositoryInterface::class);
        $this->event = $this->createMock(Event::class);

        $command = new DeleteTagCommand($this->event, 'f9c69d5b-6e68-44f0-86f2-a7e93136410c');
        $this->command = $command;
    }

    public function testDelete(): void
    {
        $tag = $this->createMock(Tag::class);

        $this->tagRepository
            ->expects(self::once())
            ->method('findOneByUuid')
            ->with('f9c69d5b-6e68-44f0-86f2-a7e93136410c')
            ->willReturn($tag);

        $tag
            ->expects(self::once())
            ->method('getEvent')
            ->willReturn($this->event);

        $handler = new DeleteTagCommandHandler($this->tagRepository);

        $this->assertEquals('f9c69d5b-6e68-44f0-86f2-a7e93136410c', ($handler)($this->command));
    }

    public function testDeleteTagNotBelongsToEvent(): void
    {
        $this->expectException(TagNotBelongsToEventException::class);

        $tag = $this->createMock(Tag::class);
        $event = $this->createMock(Event::class);

        $this->tagRepository
            ->expects(self::once())
            ->method('findOneByUuid')
            ->with('f9c69d5b-6e68-44f0-86f2-a7e93136410c')
            ->willReturn($tag);

        $tag
            ->expects(self::once())
            ->method('getEvent')
            ->willReturn($event);

        $handler = new DeleteTagCommandHandler($this->tagRepository);

        ($handler)($this->command);
    }

    public function testTagNotFound(): void
    {
        $this->expectException(TagNotFoundException::class);

        $this->tagRepository
            ->expects(self::once())
            ->method('findOneByUuid')
            ->with('f9c69d5b-6e68-44f0-86f2-a7e93136410c')
            ->willReturn(null);

        $this->tagRepository
            ->expects(self::never())
            ->method('delete');

        $handler = new DeleteTagCommandHandler($this->tagRepository);

        ($handler)($this->command);
    }
}
