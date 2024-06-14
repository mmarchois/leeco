<?php

declare(strict_types=1);

namespace App\Application\Media\Command;

use App\Application\CommandInterface;
use App\Domain\Event\Event;
use App\Domain\Media\Media;
use Symfony\Component\HttpFoundation\File\UploadedFile;

final readonly class SaveMediaCommand implements CommandInterface
{
    public function __construct(
        public Event $event,
        public UploadedFile $file,
        public string $origin,
        public ?Media $media = null,
    ) {
    }
}
