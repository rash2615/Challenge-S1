<?php

namespace App\Form;

use PHPUnit\TextUI\XmlConfiguration\CodeCoverage\Report\Text;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class ProfileType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('firstname', TextType::class, [
                'label' => 'Prénom',
                'attr' => [
                    'placeholder' => 'Prénom',
                    'required' => false
                ]
            ])
            ->add('lastname', TextType::class, [
                'label' => 'Nom',
                'attr' => [
                    'placeholder' => 'Nom',
                    'required' => false
                ]
            ])
            ->add('email', EmailType::class, [
                'label' => 'Email',
                'attr' => [
                    'placeholder' => 'addresse mail',
                    'type' => 'email',
                    'pattern' => '[a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,4}$',
                    'title' => 'Entrez une adresse mail valide',
                    'required' => false
                ]
            ])
            ->add('phone', TextType::class, [
                'label' => 'Téléphone',
                'attr' => [
                    'placeholder' => 'Téléphone',
                    'pattern' => '0[1-9]([-. ]?[0-9]{2}){4}',
                    'title' => 'Entrez un numéro de téléphone valide'
                ]
            ])
            // ->add('businessName', TextType::class, [
            //     'label' => 'Nom de l\'entreprise',
            //     'attr' => [
            //         'placeholder' => 'Nom de l\'entreprise',
            //         'required' => false
            //     ]
            // ])
            // ->add('siret', IntegerType::class, [
            //     'label' => 'Siret',
            //     'attr' => [
            //         'placeholder' => 'Siret',
            //         'pattern' => '[0-9]{14}',
            //         'required' => false
            //     ]
            // ])
            // ->add('tvaNumber', TextType::class, [
            //     'label' => 'Numéro de TVA',
            //     'attr' => [
            //         'placeholder' => 'Numéro de TVA',
            //         'required' => false
            //     ]
            // ])
            ->add('address', TextType::class, [
                'label' => 'Adresse',
                'attr' => [
                    'placeholder' => 'Adresse',
                    'required' => false
                ]
            ])
            ->add('city', TextType::class, [
                'label' => 'Ville',
                'attr' => [
                    'placeholder' => 'Ville',
                    'required' => false
                ]
            ])
            ->add('postalCode', IntegerType::class, [
                'label' => 'Code postal',
                'attr' => [
                    'placeholder' => 'Code postal',
                    'required' => false
                ]
            ])
            ->add('country', TextType::class, [
                'label' => 'Pays',
                'attr' => [
                    'placeholder' => 'Pays',
                    'required' => false
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            // Configure your form options here
        ]);
    }
}