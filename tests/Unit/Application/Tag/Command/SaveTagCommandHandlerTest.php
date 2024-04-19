<?php

declare(strict_types=1);

namespace App\Tests\Unit\Application\Tag\Command;

use App\Application\IdFactoryInterface;
use App\Application\Tag\Command\SaveTagCommand;
use App\Application\Tag\Command\SaveTagCommandHandler;
use App\Domain\Event\Event;
use App\Domain\Tag\Exception\TagAlreadyExistException;
use App\Domain\Tag\Repository\TagRepositoryInterface;
use App\Domain\Tag\Specification\IsTagAlreadyExist;
use App\Domain\Tag\Tag;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

final class SaveTagCommandHandlerTest extends TestCase
{
    private MockObject $idFactory;
    private MockObject $tagRepository;
    private MockObject $isTagAlreadyExist;
    private MockObject $event;
    private SaveTagCommand $command;
    private \DateTimeInterface $startDate;
    private \DateTimeInterface $endDate;

    public function setUp(): void
    {
        $this->idFactory = $this->createMock(IdFactoryInterface::class);
        $this->tagRepository = $this->createMock(TagRepositoryInterface::class);
        $this->isTagAlreadyExist = $this->createMock(IsTagAlreadyExist::class);
        $this->event = $this->createMock(Event::class);

        $this->startDate = new \DateTime('2023-04-25 10:00:00');
        $this->endDate = new \DateTime('2023-04-25 11:00:00');

        $command = new SaveTagCommand($this->event);
        $command->title = '   Cérémonie religieuse   '; // Voluntary add spaces
        $command->startDate = $this->startDate;
        $command->endDate = $this->endDate;

        $this->command = $command;
    }

    public function testAdd(): void
    {
        $createdTag = $this->createMock(Tag::class);
        $createdTag
            ->expects(self::once())
            ->method('getUuid')
            ->willReturn('94e78189-64bd-46f9-9b7e-d729475af557');

        $this->isTagAlreadyExist
            ->expects(self::once())
            ->method('isSatisfiedBy')
            ->with($this->event, 'Cérémonie religieuse')
            ->willReturn(false);

        $this->idFactory
            ->expects(self::once())
            ->method('make')
            ->willReturn('94e78189-64bd-46f9-9b7e-d729475af557');

        $this->tagRepository
            ->expects(self::once())
            ->method('add')
            ->with(
                new Tag(
                    uuid: '94e78189-64bd-46f9-9b7e-d729475af557',
                    title: 'Cérémonie religieuse',
                    startDate: $this->startDate,
                    endDate: $this->endDate,
                    event: $this->event,
                ),
            )
            ->willReturn($createdTag);

        $handler = new SaveTagCommandHandler(
            $this->idFactory,
            $this->tagRepository,
            $this->isTagAlreadyExist,
        );

        $this->assertEquals('94e78189-64bd-46f9-9b7e-d729475af557', ($handler)($this->command));
    }

    public function testAddAlreadyExist(): void
    {
        $this->expectException(TagAlreadyExistException::class);

        $this->isTagAlreadyExist
            ->expects(self::once())
            ->method('isSatisfiedBy')
            ->with($this->event, 'Cérémonie religieuse')
            ->willReturn(true);

        $this->idFactory
            ->expects(self::never())
            ->method('make');

        $this->tagRepository
            ->expects(self::never())
            ->method('add');

        $handler = new SaveTagCommandHandler(
            $this->idFactory,
            $this->tagRepository,
            $this->isTagAlreadyExist,
        );

        ($handler)($this->command);
    }

    public function testUpdateTitleDoesntChanged(): void
    {
        $tag = $this->createMock(Tag::class);
        $tag
            ->expects(self::once())
            ->method('getTitle')
            ->willReturn('Cérémonie religieuse');
        $tag
            ->expects(self::once())
            ->method('getUuid')
            ->willReturn('94e78189-64bd-46f9-9b7e-d729475af557');
        $tag
            ->expects(self::once())
            ->method('update')
            ->with('Cérémonie religieuse', $this->startDate, $this->endDate);

        $this->isTagAlreadyExist
            ->expects(self::never())
            ->method('isSatisfiedBy');

        $this->idFactory
            ->expects(self::never())
            ->method('make');

        $this->tagRepository
            ->expects(self::never())
            ->method('add');

        $handler = new SaveTagCommandHandler(
            $this->idFactory,
            $this->tagRepository,
            $this->isTagAlreadyExist,
        );

        $command = new SaveTagCommand($this->event, $tag);
        $command->title = '   Cérémonie religieuse  '; // Voluntary add spaces
        $command->startDate = $this->startDate;
        $command->endDate = $this->endDate;

        $this->assertEquals('94e78189-64bd-46f9-9b7e-d729475af557', ($handler)($command));
    }

    public function testUpdateTagAlreadyExist(): void
    {
        $this->expectException(TagAlreadyExistException::class);

        $tag = $this->createMock(Tag::class);
        $tag
            ->expects(self::once())
            ->method('getTitle')
            ->willReturn('Dîner');
        $tag
            ->expects(self::never())
            ->method('getUuid');
        $tag
            ->expects(self::never())
            ->method('update');

        $this->isTagAlreadyExist
            ->expects(self::once())
            ->method('isSatisfiedBy')
            ->with($this->event, 'Cérémonie religieuse')
            ->willReturn(true);

        $this->idFactory
            ->expects(self::never())
            ->method('make');

        $this->tagRepository
            ->expects(self::never())
            ->method('add');

        $handler = new SaveTagCommandHandler(
            $this->idFactory,
            $this->tagRepository,
            $this->isTagAlreadyExist,
        );

        $command = new SaveTagCommand($this->event, $tag);
        $command->title = '   Cérémonie religieuse  '; // Voluntary add spaces
        $command->startDate = $this->startDate;
        $command->endDate = $this->endDate;

        $this->assertEquals('94e78189-64bd-46f9-9b7e-d729475af557', ($handler)($command));
    }
}
