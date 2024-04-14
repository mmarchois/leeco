<?php

declare(strict_types=1);

namespace App\Infrastructure\Form\Event;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

final class EventFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', TextType::class, [
                'label' => 'event.form.title',
            ])
            ->add('date', DateType::class, [
                'label' => 'event.form.date',
                'widget' => 'single_text',
            ])
            ->add('save', SubmitType::class, [
                'label' => 'common.save',
            ])
        ;
    }
}
