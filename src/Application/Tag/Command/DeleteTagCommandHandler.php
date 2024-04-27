<?php

declare(strict_types=1);

namespace App\Application\Tag\Command;

use App\Domain\Tag\Exception\TagNotBelongsToEventException;
use App\Domain\Tag\Exception\TagNotFoundException;
use App\Domain\Tag\Repository\TagRepositoryInterface;
use App\Domain\Tag\Tag;

final readonly class DeleteTagCommandHandler
{
    public function __construct(
        private TagRepositoryInterface $tagRepository,
    ) {
    }

    public function __invoke(DeleteTagCommand $command): string
    {
        $tag = $this->tagRepository->findOneByUuid($command->uuid);
        if (!$tag instanceof Tag) {
            throw new TagNotFoundException();
        }

        if ($tag->getEvent() !== $command->event) {
            throw new TagNotBelongsToEventException();
        }

        $this->tagRepository->delete($tag);

        return $command->uuid;
    }
}
