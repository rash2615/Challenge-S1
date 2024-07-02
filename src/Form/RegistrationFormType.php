<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
// use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;

class RegistrationFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('firstname', TextType::class, [
                'label' => 'Prénom',
            ])
            ->add('lastname', TextType::class, [
                'label' => 'Nom',
            ])
            ->add('email', EmailType::class, [
                'label' => 'Adresse email',
            ])
            // ->add('plainPassword', RepeatedType::class, [
            //     'type' => PasswordType::class,
            //     'first_options' => ['label' => 'Mot de passe'],
            //     'second_options' => ['label' => 'Confirmation du mot de passe'],
            //     'mapped' => false,
            // ])
            ->add('password', PasswordType::class, [
                'label' => 'Mot de passe',
                'attr' => ['autocomplete' => 'new-password'],
            ])
            // ->add('confirmplainPassword', PasswordType::class, [
            //     'label' => 'Confirmation du mot de passe',
            //     'attr' => ['autocomplete' => 'new-password'],
            // ])
            ->add('phone', TextType::class, [
                'label' => 'Téléphone',
            ])
            ->add('phone', TextType::class, [
                'label' => 'Téléphone',
            ])
            ->add('businessName', TextType::class, [
                'label' => 'Nom de la société',
            ])
            ->add('address', TextType::class, [
                'label' => 'Adresse de la société',
            ])
            ->add('siret', TextType::class, [
                'label' => 'Numéro de SIRET',
            ])
            ->add('tvaNumber', TextType::class, [
                'label' => 'Numéro de TVA',
            ])
            ->add('city', TextType::class, [
                'label' => 'Ville',
            ])
            ->add('postalCode', TextType::class, [
                'label' => 'Code Postal',
            ])
            ->add('country', TextType::class, [
                'label' => 'Pays',
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}