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
            ->add('crypto', EntityType::class, [
                'class' => Crypto::class,
                'choice_label' => function(Crypto $crypto) {
                    return $crypto->getName() . ' (' . $crypto->getSymbol() . ')';
                },
                'label' => 'Cryptomonnaie',
                'attr' => ['class' => 'form-select'],
                'placeholder' => 'Sélectionner une cryptomonnaie'
            ])
            ->add('type', ChoiceType::class, [
                'label' => 'Type d\'opération',
                'choices' => [
                    'Achat' => Transaction::TYPE_BUY,
                    'Récompense de staking' => Transaction::TYPE_STAKE_REWARD,
                ],
                'attr' => ['class' => 'form-select'],
            ])
            ->add('date', null, [
                'label' => 'Date',
                'widget' => 'single_text',
                'attr' => ['class' => 'form-control'],
            ])
            ->add('quantity', NumberType::class, [
                'label' => 'Quantité',
                'scale' => 10,
                'attr' => ['step' => '0.0000000001', 'class' => 'form-control', 'placeholder' => '0.0000000000']
            ])
            ->add('unit_price_usd', NumberType::class, [
                'label' => 'Prix unitaire (USD)',
                'required' => false,
                'scale' => 8,
                'attr' => ['step' => '0.00000001', 'class' => 'form-control', 'placeholder' => '0.00']
            ])
            ->add('fee_usd', NumberType::class, [
                'label' => 'Frais (USD)',
                'required' => false,
                'scale' => 8,
                'attr' => ['step' => '0.00000001', 'class' => 'form-control', 'placeholder' => '0.00']
            ])
            ->add('note', TextareaType::class, [
                'label' => 'Note (optionnel)',
                'required' => false,
                'attr' => ['rows' => 3, 'class' => 'form-control', 'placeholder' => 'Ajoutez une note...']
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
