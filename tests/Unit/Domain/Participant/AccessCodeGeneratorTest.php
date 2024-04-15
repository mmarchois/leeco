<?php

declare(strict_types=1);

namespace App\Tests\Unit\Domain\Participant;

use App\Domain\Participant\AccessCodeGenerator;
use PHPUnit\Framework\TestCase;

final class AccessCodeGeneratorTest extends TestCase
{
    public function testGenerate(): void
    {
        $accessCodeGenerator = new AccessCodeGenerator();

        $this->assertNotEmpty($accessCodeGenerator->generate());
        $this->assertSame(12, \strlen($accessCodeGenerator->generate()));
    }
}
