<?php

namespace App\Form;

use App\Entity\Crypto;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Length;

class CryptoType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'label' => 'Nom de la cryptomonnaie',
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'Ex: Bitcoin'
                ],
                'constraints' => [
                    new NotBlank([
                        'message' => 'Veuillez saisir le nom de la cryptomonnaie',
                    ]),
                    new Length([
                        'min' => 2,
                        'max' => 255,
                        'minMessage' => 'Le nom doit contenir au moins {{ limit }} caractères',
                        'maxMessage' => 'Le nom ne peut pas dépasser {{ limit }} caractères',
                    ]),
                ],
            ])
            ->add('symbol', TextType::class, [
                'label' => 'Symbole',
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'Ex: BTC',
                    'style' => 'text-transform: uppercase;'
                ],
                'constraints' => [
                    new NotBlank([
                        'message' => 'Veuillez saisir le symbole de la cryptomonnaie',
                    ]),
                    new Length([
                        'min' => 2,
                        'max' => 10,
                        'minMessage' => 'Le symbole doit contenir au moins {{ limit }} caractères',
                        'maxMessage' => 'Le symbole ne peut pas dépasser {{ limit }} caractères',
                    ]),
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Crypto::class,
        ]);
    }
}
