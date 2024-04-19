<?php

declare(strict_types=1);

namespace App\Infrastructure\Adapter;

use App\Application\DateUtilsInterface;

final class DateUtils implements DateUtilsInterface
{
    private \DateTimeZone $clientTimezone;

    public function __construct(
        string $clientTimezone,
    ) {
        $this->clientTimezone = new \DateTimeZone($clientTimezone);
    }

    public function getNow(): \DateTimeImmutable
    {
        return \DateTimeImmutable::createFromInterface(
            new \DateTime('now'),
        )->setTimeZone($this->clientTimezone);
    }

    public function addDaysToDate(\DateTimeInterface $date, int $days): \DateTimeImmutable
    {
        return (new \DateTimeImmutable($date->format('Y-m-d')))
            ->add(new \DateInterval(sprintf('P%dD', $days)))
            ->setTimeZone($this->clientTimezone);
    }
}
