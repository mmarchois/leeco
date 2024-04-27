<?php

declare(strict_types=1);

namespace App\Infrastructure\Twig;

final class AppExtension extends \Twig\Extension\AbstractExtension
{
    private \DateTimeZone $clientTimezone;

    public function __construct(
        string $clientTimezone,
    ) {
        $this->clientTimezone = new \DateTimeZone($clientTimezone);
    }

    public function getFunctions(): array
    {
        return [
            new \Twig\TwigFunction('app_datetime', [$this, 'formatDateTime']),
            new \Twig\TwigFunction('app_date', [$this, 'formatDate']),
        ];
    }

    public function formatDateTime(\DateTimeInterface $date): string
    {
        $dateTime = \DateTimeImmutable::createFromInterface($date)->setTimeZone($this->clientTimezone);

        return $dateTime->format('d/m/Y à H\hi');
    }

    public function formatDate(\DateTimeInterface $date): string
    {
        $dateTime = \DateTimeImmutable::createFromInterface($date)->setTimeZone($this->clientTimezone);

        return $dateTime->format('d/m/Y');
    }
}
