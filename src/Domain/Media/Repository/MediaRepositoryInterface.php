<?php

declare(strict_types=1);

namespace App\Domain\Media\Repository;

use App\Domain\Media\Media;

interface MediaRepositoryInterface
{
    public function add(Media $media): Media;
}
