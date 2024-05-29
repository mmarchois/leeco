<?php

declare(strict_types=1);

namespace App\Domain\Media;

enum MediaTypeEnum: string
{
    case IMAGE = 'IMAGE';
    case VIDEO = 'VIDEO';
}
