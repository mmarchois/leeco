<?php

declare(strict_types=1);

namespace App\Infrastructure\Form\Tag;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

final class TagFormType extends AbstractType
{
    public function __construct(
        private string $clientTimezone,
    ) {
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', TextType::class, [
                'label' => 'tag.form.title',
            ])
            ->add('startDate', DateTimeType::class, [
                'label' => 'tag.form.startDate',
                'widget' => 'single_text',
                'view_timezone' => $this->clientTimezone,
            ])
            ->add('endDate', DateTimeType::class, [
                'label' => 'tag.form.endDate',
                'widget' => 'single_text',
                'view_timezone' => $this->clientTimezone,
            ])
            ->add('save', SubmitType::class, [
                'label' => 'common.save',
            ])
        ;
    }
}
