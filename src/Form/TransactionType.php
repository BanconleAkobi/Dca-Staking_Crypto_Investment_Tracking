<?php

namespace App\Form;

use App\Entity\Crypto;
use App\Entity\Transaction;
use App\Entity\user;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TransactionType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('type', ChoiceType::class, [
                'choices' => [
                    'Achat (BUY)' => Transaction::TYPE_BUY,
                    'Reward of stacking' => Transaction::TYPE_STAKE_REWARD,
                ],
                'attr' => ['class' => 'form-select'],
            ])
            ->add('date', null, [
                'widget' => 'single_text',
                'attr' => ['class' => 'form-select'],
            ])
            ->add('unit_price_usd', NumberType::class, [
                'required' => false,
                'scale' => 8,
                'attr' => ['step' => '0.00000001', 'class' => 'form-control']
            ])
            ->add('quantity', NumberType::class, [
                'scale' => 10,
                'attr' => ['step' => '0.0000000001', 'class' => 'form-control']
                ])
            ->add('fee_usd', NumberType::class, [
                'required' => false,
                'scale' => 8,
                'attr' => ['step' => '0.00000001', 'class' => 'form-control']
            ])
            ->add('note', TextareaType::class, [
                'required' => false,
                'attr' => ['rows' => 2, 'class' => 'form-control']
            ])
            ->add('crypto', EntityType::class, [
                'class' => Crypto::class,          // (ou Crypto::class)
                'choice_label' => 'symbol',       // affichera BTC, SOL, ETH
                'placeholder' => 'Select a crypto…',
                'attr' => ['class' => 'form-select'],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Transaction::class,
        ]);
    }
}
