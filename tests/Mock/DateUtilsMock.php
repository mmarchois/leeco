<?php

declare(strict_types=1);

namespace App\Tests\Mock;

use App\Application\DateUtilsInterface;

final class DateUtilsMock implements DateUtilsInterface
{
    public function getNow(): \DateTimeImmutable
    {
        return new \DateTimeImmutable('2023-08-25');
    }
}
