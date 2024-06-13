<?php

namespace App\Form;

use App\Entity\Devis;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class DevisType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('customer_id')
            ->add('chrono')
            ->add('status')
            ->add('validity_date', null, [
                'widget' => 'single_text',
            ])
            ->add('created_at', null, [
                'widget' => 'single_text',
            ])
            ->add('work_start_date', null, [
                'widget' => 'single_text',
            ])
            ->add('work_duration')
            ->add('payment_deadline', null, [
                'widget' => 'single_text',
            ])
            ->add('payment_delay_rate')
            ->add('tva_applicable')
            ->add('sent_at', null, [
                'widget' => 'single_text',
            ])
            ->add('signed_at', null, [
                'widget' => 'single_text',
            ])
            ->add('is_draft')
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Devis::class,
        ]);
    }
}
