<?php

declare(strict_types=1);

namespace App\Application\Tag\Command;

use App\Application\IdFactoryInterface;
use App\Domain\Tag\Exception\TagAlreadyExistException;
use App\Domain\Tag\Repository\TagRepositoryInterface;
use App\Domain\Tag\Specification\IsTagAlreadyExist;
use App\Domain\Tag\Tag;

final readonly class SaveTagCommandHandler
{
    public function __construct(
        private IdFactoryInterface $idFactory,
        private TagRepositoryInterface $tagRepository,
        private IsTagAlreadyExist $isTagAlreadyExist,
    ) {
    }

    public function __invoke(SaveTagCommand $command): string
    {
        $title = trim($command->title);

        // Update tag

        if ($command->tag) {
            if ($title !== $command->tag->getTitle()
                && $this->isTagAlreadyExist->isSatisfiedBy($command->event, $title)
            ) {
                throw new TagAlreadyExistException();
            }

            $command->tag->update(
                $title,
                $command->startDate,
                $command->endDate,
            );

            return $command->tag->getUuid();
        }

        // Create tag

        if ($this->isTagAlreadyExist->isSatisfiedBy($command->event, $title)) {
            throw new TagAlreadyExistException();
        }

        $tag = $this->tagRepository->add(
            new Tag(
                uuid: $this->idFactory->make(),
                title: $title,
                startDate: $command->startDate,
                endDate: $command->endDate,
                event: $command->event,
            ),
        );

        return $tag->getUuid();
    }
}
