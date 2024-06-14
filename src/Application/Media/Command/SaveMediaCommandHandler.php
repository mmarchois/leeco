<?php

declare(strict_types=1);

namespace App\Application\Media\Command;

use App\Application\DateUtilsInterface;
use App\Application\IdFactoryInterface;
use App\Application\StorageInterface;
use App\Domain\Media\Media;
use App\Domain\Media\Repository\MediaRepositoryInterface;

final readonly class SaveMediaCommandHandler
{
    public function __construct(
        private StorageInterface $storage,
        private IdFactoryInterface $idFactory,
        private DateUtilsInterface $dateUtils,
        private MediaRepositoryInterface $mediaRepository,
    ) {
    }

    public function __invoke(SaveMediaCommand $command): Media
    {
        $eventUuid = $command->event->getUuid();

        // Update media
        if ($media = $command->media) {
            $this->storage->delete($media->getPath());
            $path = $this->storage->write($eventUuid, $media->getUuid(), $command->file);
            $command->media->updatePath($path);

            return $command->media;
        }

        // Create media
        $uuid = $this->idFactory->make();
        $path = $this->storage->write($eventUuid, $uuid, $command->file);

        return $this->mediaRepository->add(
            new Media(
                uuid: $uuid,
                path: $path,
                origin: $command->origin,
                createdAt: $this->dateUtils->getNow(),
                event: $command->event,
            ),
        );
    }
}
