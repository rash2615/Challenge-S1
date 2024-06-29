<?php

namespace App\Form;

use App\Entity\Customer;
use App\Entity\Invoice;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class InvoiceType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $this->listUsers = $options['list_users'];

        $classStyle = 'focus:shadow-primary-outline dark:bg-slate-850 dark:text-white text-sm leading-5.6 ease block rounded-lg border border-solid border-gray-300 bg-white bg-clip-padding px-3 py-2 font-normal text-gray-700 outline-none transition-all placeholder:text-gray-500 focus:border-blue-500 focus:outline-none';

        $builder
            ->add('customer', EntityType::class, [
                'class' => Customer::class,
                'choices' => $this->listUsers,
                'choice_label' => function (Customer $customer): string
                {
                    return $customer->getFirstname() . ' ' . $customer->getLastname();
                },
                'attr' => [
                    'placeholder' => 'Client',
                    'required' => true,
                    'title' => 'Sélectionnez le client',
                    'class' => $classStyle,
                ]
            ])
            ->add('service_done_at', DateType::class, [
                "label" => "Date de réalisation de la prestation",
                'attr' => [
                    'class' => $classStyle
                ]
            ])
            ->add('payment_deadline', DateType::class, [
                "label" => "Date limite de paiement",
                'attr' => [
                    'class' => $classStyle
                ]
            ])
            ->add('payment_delay_rate', IntegerType::class, [
                "label" => "Taux de pénalité en cas de retard",
                'help' => "Valeur en pourcentage",
                'attr' => [
                    'class' => $classStyle,
                    'min' => '0',
                    'max' => '100'
                ]
            ])
            ->add('tva_applicable', CheckboxType::class, [
                'attr' => [
                    'class' => $classStyle,
                    'required'   => false,
                    'empty_data' => false
                ]
            ])
            ->add('is_draft', CheckboxType::class, [
                'attr' => [
                    'class' => $classStyle,
                    'required'   => false,
                    'empty_data' => false
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Invoice::class,
            'list_users' => null
        ]);
    }
}
