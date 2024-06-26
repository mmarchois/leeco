<?php

declare(strict_types=1);

namespace App\Domain\Event;

final class AccessCodeGenerator
{
    public function generate(): string
    {
        return bin2hex(random_bytes(6));
    }
}
