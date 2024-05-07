<?php

declare(strict_types=1);

namespace App\Infrastructure\Validator;

use App\Application\Tag\Command\SaveTagCommand;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedValueException;

final class SaveTagCommandConstraintValidator extends ConstraintValidator
{
    private \DateTimeZone $clientTimezone;

    public function __construct(
        string $clientTimezone,
    ) {
        $this->clientTimezone = new \DateTimeZone($clientTimezone);
    }

    public function validate(mixed $command, Constraint $constraint): void
    {
        if (!$command instanceof SaveTagCommand) {
            throw new UnexpectedValueException($command, SaveTagCommand::class);
        }

        $startDate = \DateTimeImmutable::createFromInterface($command->startDate)
            ->setTimeZone($this->clientTimezone);

        $endDate = \DateTimeImmutable::createFromInterface($command->endDate)
            ->setTimeZone($this->clientTimezone);

        if ($endDate < $startDate) {
            $this->context->buildViolation('tag.error.end_date_before_start_date')
                ->setParameter('{{ compared_value }}', $startDate->format('d/m/Y'))
                ->atPath('endDate')
                ->addViolation();
        }

        $eventDate = \DateTimeImmutable::createFromInterface($command->event->getStartDate())
            ->setTimeZone($this->clientTimezone);

        if ($eventDate > $startDate) {
            $this->context->buildViolation('tag.error.start_date_after_event_start_date')
                ->setParameter('{{ compared_value }}', $eventDate->format('d/m/Y'))
                ->atPath('startDate')
                ->addViolation();
        }
    }
}
