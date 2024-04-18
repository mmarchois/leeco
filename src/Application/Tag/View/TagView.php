<?php

declare(strict_types=1);

namespace App\Application\Tag\View;

final readonly class TagView
{
    public function __construct(
        public string $uuid,
        public string $title,
        public \DateTimeInterface $startDate,
        public \DateTimeInterface $endDate,
    ) {
    }
}
