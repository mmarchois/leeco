<?php

declare(strict_types=1);

namespace App\Tests\Unit\Domain\Tag\Specification;

use App\Domain\Event\Event;
use App\Domain\Tag\Repository\TagRepositoryInterface;
use App\Domain\Tag\Specification\IsTagAlreadyExist;
use App\Domain\Tag\Tag;
use PHPUnit\Framework\TestCase;

final class IsTagAlreadyExistTest extends TestCase
{
    public function testTagExist(): void
    {
        $event = $this->createMock(Event::class);
        $tag = $this->createMock(Tag::class);
        $tagRepository = $this->createMock(TagRepositoryInterface::class);
        $tagRepository
            ->expects(self::once())
            ->method('findOneByEventAndTitle')
            ->with($event, 'Cérémonie religieuse')
            ->willReturn($tag);

        $pattern = new IsTagAlreadyExist($tagRepository);
        $this->assertTrue($pattern->isSatisfiedBy($event, 'Cérémonie religieuse'));
    }

    public function testTagDoesntExist(): void
    {
        $event = $this->createMock(Event::class);
        $tagRepository = $this->createMock(TagRepositoryInterface::class);
        $tagRepository
            ->expects(self::once())
            ->method('findOneByEventAndTitle')
            ->with($event, 'Cérémonie religieuse')
            ->willReturn(null);

        $pattern = new IsTagAlreadyExist($tagRepository);
        $this->assertFalse($pattern->isSatisfiedBy($event, 'Cérémonie religieuse'));
    }
}
