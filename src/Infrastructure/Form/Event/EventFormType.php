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
    public function __construct(
        private string $clientTimezone,
    ) {
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', TextType::class, [
                'label' => 'event.form.title',
            ])
            ->add('startDate', DateType::class, [
                'label' => 'event.form.startDate',
                'help' => 'event.form.startDate.help',
                'widget' => 'single_text',
                'view_timezone' => $this->clientTimezone,
            ])
            ->add('endDate', DateType::class, [
                'label' => 'event.form.endDate',
                'help' => 'event.form.endDate.help',
                'widget' => 'single_text',
                'view_timezone' => $this->clientTimezone,
            ])
            ->add('save', SubmitType::class, [
                'label' => 'common.save',
            ])
        ;
    }
}
