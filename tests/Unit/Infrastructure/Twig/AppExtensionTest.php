<?php

declare(strict_types=1);

namespace App\Test\Unit\Infrastructure\Twig;

use App\Infrastructure\Twig\AppExtension;
use App\Tests\TimezoneHelper;
use PHPUnit\Framework\TestCase;

class AppExtensionTest extends TestCase
{
    use TimezoneHelper;

    private AppExtension $extension;

    protected function setUp(): void
    {
        $this->extension = new AppExtension(
            'Etc/GMT-1', // Independent of Daylight Saving Time (DST).
        );
    }

    public function testGetFunctions(): void
    {
        $this->assertCount(2, $this->extension->getFunctions());
    }

    public function testFormatDate(): void
    {
        $this->assertSame('06/01/2023', $this->extension->formatDate(new \DateTimeImmutable('2023-01-06')));
        $this->assertSame('06/01/2023', $this->extension->formatDate(new \DateTimeImmutable('2023-01-06T08:30:00')));
    }

    public function testFormatDateTime(): void
    {
        $this->assertSame(
            '06/01/2023 à 09h30',
            $this->extension->formatDateTime(new \DateTimeImmutable('2023-01-06T08:30:00')),
        );
        $this->assertSame(
            '07/01/2023 à 11h30',
            $this->extension->formatDateTime(new \DateTimeImmutable('2023-01-07T10:30:00')),
        );
    }
}
