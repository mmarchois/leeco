<?php

declare(strict_types=1);

namespace App\Domain\Media;

enum MediaTypeEnum: string
{
    case EVENT_BANNER = 'EVENT_BANNER';
    case GUEST = 'GUEST';
}
