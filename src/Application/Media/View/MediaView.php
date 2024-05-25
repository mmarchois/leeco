<?php

declare(strict_types=1);

namespace App\Application\Media\View;

final readonly class MediaView
{
    public function __construct(
        public string $uuid,
        public string $path,
        public string $author,
    ) {
    }
}
