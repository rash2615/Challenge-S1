<?php

namespace App\Form;

use App\Entity\Customer;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

class CustomerType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('firstname', TextType::class, [
                'label' => 'Prénom',
                'attr' => [
                    'placeholder' => 'Prénom',
                    'required' => true
                ]
            ])
            ->add('lastname', TextType::class, [
                'label' => 'Nom',
                'attr' => [
                    'placeholder' => 'Nom',
                    'required' => false
                ]
            ])
            ->add('email', TextType::class, [
                'label' => 'Email',
                'attr' => [
                    'placeholder' => 'addresse mail',
                    'type' => 'email',
                    'pattern' => '[a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,4}$',
                    'title' => 'Entrez une adresse mail valide',
                    'required' => true
                ]
            ])
            ->add('type', ChoiceType::class, [
                'label' => 'Type',
                'choices' => [
                    'Particulier' => 'PERSON',
                    'Professionnel' => 'COMPANY'
                ],
                'attr' => [
                    'placeholder' => 'Type',
                    'required' => true,
                    'title' => 'Sélectionnez le type de client'
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
            ->add('company', TextType::class, [
                'label' => 'Entreprise',
                'attr' => [
                    'placeholder' => 'Entreprise',
                    'required'   => false,
                    'empty_data' => false,
                    'title' => 'Entrez le nom de l\'entreprise'
                ]
            ])
            ->add('siret', IntegerType::class, [
                'label' => 'Siret',
                'attr' => [
                    'placeholder' => 'Siret',
                    'required'   => false,
                    'empty_data' => false,
                    'pattern' => '[0-9]{14}'
                ]
            ])
            ->add('address', TextType::class, [
                'label' => 'Adresse',
                'attr' => [
                    'placeholder' => 'Adresse',
                    'required' => false,
                    'title' => 'Entrez l\'adresse du client'
                ]
            ])
            ->add('postal_code', IntegerType::class, [
                'label' => 'Code postal',
                'attr' => [
                    'placeholder' => 'Code postal',
                    'pattern' => '[0-9]{5}',
                    'title' => 'Entrez un code postal valide'
                ]
            ])
            ->add('city', TextType::class, [
                'label' => 'Ville',
                'attr' => [
                    'placeholder' => 'Ville',
                    'required' => false,
                    'title' => 'Entrez le nom de la ville'
                ]
            ])
            ->add('country', TextType::class, [
                'label' => 'Pays',
                'attr' => [
                    'placeholder' => 'Pays',
                    'required' => false,
                    'title' => 'Entrez le nom du pays',
                    'help' => 'FRA pour la France'
                ]
            ])
        ;

    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Customer::class,
        ]);
    }
}
