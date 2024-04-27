<?php

declare(strict_types=1);

namespace App\Test\Unit\Infrastructure\Validation;

use App\Infrastructure\Validator\SaveTagCommandConstraint;
use App\Infrastructure\Validator\SaveTagCommandConstraintValidator;
use Symfony\Component\Validator\ConstraintValidatorInterface;
use Symfony\Component\Validator\Exception\UnexpectedValueException;
use Symfony\Component\Validator\Test\ConstraintValidatorTestCase;

class SaveTagCommandConstraintValidatorTest extends ConstraintValidatorTestCase
{
    protected function createValidator(): ConstraintValidatorInterface
    {
        return new SaveTagCommandConstraintValidator('Europe/Paris');
    }

    public function testUnexpectedValue(): void
    {
        $this->expectException(UnexpectedValueException::class);
        $this->validator->validate('not a command instance', new SaveTagCommandConstraint());
    }
}
