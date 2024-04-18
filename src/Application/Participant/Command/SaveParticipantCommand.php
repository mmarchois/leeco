<?php

declare(strict_types=1);

namespace App\Application\Participant\Command;

use App\Application\CommandInterface;
use App\Domain\Event\Event;
use App\Domain\Participant\Participant;

final class SaveParticipantCommand implements CommandInterface
{
    public ?string $firstName = null;
    public ?string $lastName = null;
    public ?string $email = null;

    public function __construct(
        public readonly Event $event,
        public readonly ?Participant $participant = null,
    ) {
    }

    public static function create(Event $event, Participant $participant): self
    {
        $command = new self($event, $participant);
        $command->firstName = $participant->getFirstName();
        $command->lastName = $participant->getLastName();
        $command->email = $participant->getEmail();

        return $command;
    }
}
