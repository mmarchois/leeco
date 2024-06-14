<?php

declare(strict_types=1);

namespace App\Domain\Media;

enum MediaOriginEnum: string
{
    case CAMERA = 'CAMERA';
    case PHOTOBOOTH = 'PHOTOBOOTH';
}
