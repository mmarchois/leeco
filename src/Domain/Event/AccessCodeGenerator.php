<?php

declare(strict_types=1);

namespace App\Domain\Event;

final class AccessCodeGenerator
{
    public function generate(): string
    {
        return base64_encode(random_bytes(8));
    }
}
