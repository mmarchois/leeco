<?php

declare(strict_types=1);

namespace App\Infrastructure\Form\Participant;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

final class ParticipantFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('firstName', TextType::class, [
                'label' => 'participant.form.firstName',
            ])
            ->add('lastName', TextType::class, [
                'label' => 'participant.form.lastName',
            ])
            ->add('email', EmailType::class, [
                'label' => 'participant.form.email',
            ])
            ->add('save', SubmitType::class, [
                'label' => 'common.save',
            ])
        ;
    }
}
