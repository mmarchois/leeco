<?php

declare(strict_types=1);

namespace App\Tests\Unit\Application\Media\Command;

use App\Application\DateUtilsInterface;
use App\Application\IdFactoryInterface;
use App\Application\Media\Command\SaveMediaCommand;
use App\Application\Media\Command\SaveMediaCommandHandler;
use App\Application\StorageInterface;
use App\Domain\Event\Event;
use App\Domain\Media\Media;
use App\Domain\Media\MediaOriginEnum;
use App\Domain\Media\Repository\MediaRepositoryInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\File\UploadedFile;

final class SaveMediaCommandHandlerTest extends TestCase
{
    private MockObject $idFactory;
    private MockObject $dateUtils;
    private MockObject $mediaRepository;
    private MockObject $storage;
    private MockObject $file;

    public function setUp(): void
    {
        $this->idFactory = $this->createMock(IdFactoryInterface::class);
        $this->dateUtils = $this->createMock(DateUtilsInterface::class);
        $this->mediaRepository = $this->createMock(MediaRepositoryInterface::class);
        $this->storage = $this->createMock(StorageInterface::class);
        $this->file = $this->createMock(UploadedFile::class);
    }

    public function testAdd(): void
    {
        $createdMedia = $this->createMock(Media::class);
        $event = $this->createMock(Event::class);
        $event
            ->expects(self::once())
            ->method('getUuid')
            ->willReturn('2e131626-40a5-42d0-857a-f285e1f2bb54');

        $createdAt = new \DateTimeImmutable('2024-05-24');

        $this->idFactory
            ->expects(self::once())
            ->method('make')
            ->willReturn('94e78189-64bd-46f9-9b7e-d729475af557');

        $this->storage
            ->expects(self::once())
            ->method('write')
            ->with('2e131626-40a5-42d0-857a-f285e1f2bb54', '94e78189-64bd-46f9-9b7e-d729475af557', $this->file)
            ->willReturn('2e131626-40a5-42d0-857a-f285e1f2bb54/94e78189-64bd-46f9-9b7e-d729475af557.jpg');

        $this->dateUtils
            ->expects(self::once())
            ->method('getNow')
            ->willReturn($createdAt);

        $this->mediaRepository
            ->expects(self::once())
            ->method('add')
            ->with(
                new Media(
                    uuid: '94e78189-64bd-46f9-9b7e-d729475af557',
                    path: '2e131626-40a5-42d0-857a-f285e1f2bb54/94e78189-64bd-46f9-9b7e-d729475af557.jpg',
                    origin: MediaOriginEnum::CAMERA->value,
                    createdAt: $createdAt,
                    event: $event,
                ),
            )
            ->willReturn($createdMedia);

        $command = new SaveMediaCommand($event, $this->file, MediaOriginEnum::CAMERA->value);
        $handler = new SaveMediaCommandHandler(
            $this->storage,
            $this->idFactory,
            $this->dateUtils,
            $this->mediaRepository,
        );

        $this->assertEquals($createdMedia, ($handler)($command));
    }

    public function testUpdate(): void
    {
        $event = $this->createMock(Event::class);
        $event
            ->expects(self::once())
            ->method('getUuid')
            ->willReturn('2e131626-40a5-42d0-857a-f285e1f2bb54');
        $media = $this->createMock(Media::class);
        $media
            ->expects(self::once())
            ->method('getPath')
            ->willReturn('2e131626-40a5-42d0-857a-f285e1f2bb54/94e78189-64bd-46f9-9b7e-d729475af557.jpg');
        $media
            ->expects(self::once())
            ->method('updatePath')
            ->with('2e131626-40a5-42d0-857a-f285e1f2bb54/94e78189-64bd-46f9-9b7e-d729475af557.jpg');
        $media
            ->expects(self::once())
            ->method('getUuid')
            ->willReturn('94e78189-64bd-46f9-9b7e-d729475af557');

        $this->idFactory
            ->expects(self::never())
            ->method('make');

        $this->storage
            ->expects(self::once())
            ->method('delete')
            ->with('2e131626-40a5-42d0-857a-f285e1f2bb54/94e78189-64bd-46f9-9b7e-d729475af557.jpg');

        $this->storage
            ->expects(self::once())
            ->method('write')
            ->with('2e131626-40a5-42d0-857a-f285e1f2bb54', '94e78189-64bd-46f9-9b7e-d729475af557', $this->file)
            ->willReturn('2e131626-40a5-42d0-857a-f285e1f2bb54/94e78189-64bd-46f9-9b7e-d729475af557.jpg');

        $this->dateUtils
            ->expects(self::never())
            ->method('getNow');

        $this->mediaRepository
            ->expects(self::never())
            ->method('add');

        $command = new SaveMediaCommand($event, $this->file, MediaOriginEnum::CAMERA->value, $media);
        $handler = new SaveMediaCommandHandler(
            $this->storage,
            $this->idFactory,
            $this->dateUtils,
            $this->mediaRepository,
        );

        $this->assertEquals($media, ($handler)($command));
    }
}
