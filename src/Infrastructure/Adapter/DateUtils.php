<?php

declare(strict_types=1);

namespace App\Infrastructure\Adapter;

use App\Application\DateUtilsInterface;

final class DateUtils implements DateUtilsInterface
{
    public function getNow(): \DateTimeImmutable
    {
        return new \DateTimeImmutable('now');
    }

    public function addDaysToDate(\DateTimeInterface $date, int $days): \DateTimeImmutable
    {
        return (new \DateTimeImmutable($date->format('Y-m-d')))
            ->add(new \DateInterval(sprintf('P%dD', $days)));
    }
}
