<?php

declare(strict_types=1);

namespace App\Infrastructure\Form\User;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

final class ProfileFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('firstName', TextType::class, [
                'label' => 'profile.form.firstName',
            ])
            ->add('lastName', TextType::class, [
                'label' => 'profile.form.lastName',
            ])
            ->add('email', EmailType::class, [
                'label' => 'profile.form.email',
            ])
            ->add('save', SubmitType::class, [
                'label' => 'common.save',
            ])
        ;
    }
}
