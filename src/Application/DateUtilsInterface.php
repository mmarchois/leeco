<?php

declare(strict_types=1);

namespace App\Application;

interface DateUtilsInterface
{
    public function getNow(): \DateTimeImmutable;

    public function addDaysToDate(\DateTimeInterface $date, int $days): \DateTimeImmutable;
}
