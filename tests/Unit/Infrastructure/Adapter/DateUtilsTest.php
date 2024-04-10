<?php

declare(strict_types=1);

namespace App\Tests\Unit\Infrastructure\Adapter;

use App\Infrastructure\Adapter\DateUtils;
use PHPUnit\Framework\TestCase;

final class DateUtilsTest extends TestCase
{
    public function testNow(): void
    {
        $dateUtils = new DateUtils();

        $this->assertEquals((new \DateTimeImmutable('now'))->format('Y-m-d'), $dateUtils->getNow()->format('Y-m-d'));
    }

    public function testAddDaysToDate(): void
    {
        $dateUtils = new DateUtils();

        $this->assertEquals(
            (new \DateTimeImmutable('2023-02-19'))->format('Y-m-d'),
            $dateUtils->addDaysToDate(new \DateTime('2023-01-20'), 30)->format('Y-m-d'),
        );

        $this->assertEquals(
            (new \DateTimeImmutable('2023-01-25'))->format('Y-m-d'),
            $dateUtils->addDaysToDate(new \DateTime('2023-01-20'), 5)->format('Y-m-d'),
        );
    }
}
