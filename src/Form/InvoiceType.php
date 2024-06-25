<?php

namespace App\Form;

use App\Entity\Invoice;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class InvoiceType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('customer_id')
            ->add('status')
            ->add('sent_at', null, [
                'widget' => 'single_text',
            ])
            ->add('paid_at', null, [
                'widget' => 'single_text',
            ])
            ->add('chrono')
            ->add('tva_applicable')
            ->add('created_at', null, [
                'widget' => 'single_text',
            ])
            ->add('service_done_at', null, [
                'widget' => 'single_text',
            ])
            ->add('payment_deadline', null, [
                'widget' => 'single_text',
            ])
            ->add('payment_delay_rate')
            ->add('is_draft')
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Invoice::class,
        ]);
    }
}
