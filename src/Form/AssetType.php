<?php

namespace App\Form;

use App\Entity\Asset;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;

class AssetType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'label' => 'Nom de l\'actif',
                'attr' => [
                    'class' => 'form-control modern-input',
                    'placeholder' => 'Ex: Bitcoin, Apple Inc., SPDR S&P 500 ETF',
                    'data-bs-toggle' => 'tooltip',
                    'data-bs-placement' => 'top',
                    'title' => 'Nom complet de l\'actif'
                ],
                'constraints' => [
                    new Assert\NotBlank(['message' => 'Le nom de l\'actif est obligatoire']),
                    new Assert\Length([
                        'min' => 2,
                        'max' => 255,
                        'minMessage' => 'Le nom doit contenir au moins {{ limit }} caractères',
                        'maxMessage' => 'Le nom ne peut pas dépasser {{ limit }} caractères'
                    ])
                ]
            ])
            ->add('symbol', TextType::class, [
                'label' => 'Symbole',
                'attr' => [
                    'class' => 'form-control modern-input',
                    'placeholder' => 'Ex: BTC, AAPL, SPY',
                    'style' => 'text-transform: uppercase',
                    'data-bs-toggle' => 'tooltip',
                    'data-bs-placement' => 'top',
                    'title' => 'Symbole de trading (sera automatiquement mis en majuscules)'
                ],
                'constraints' => [
                    new Assert\NotBlank(['message' => 'Le symbole est obligatoire']),
                    new Assert\Length([
                        'min' => 1,
                        'max' => 10,
                        'minMessage' => 'Le symbole doit contenir au moins {{ limit }} caractère',
                        'maxMessage' => 'Le symbole ne peut pas dépasser {{ limit }} caractères'
                    ])
                ]
            ])
            ->add('type', ChoiceType::class, [
                'label' => 'Type d\'actif',
                'choices' => [
                    'Cryptomonnaie' => Asset::TYPE_CRYPTO,
                    'Action' => Asset::TYPE_STOCK,
                    'ETF' => Asset::TYPE_ETF,
                    'Obligation' => Asset::TYPE_BOND,
                    'Épargne' => Asset::TYPE_SAVINGS,
                    'Immobilier' => Asset::TYPE_REAL_ESTATE,
                    'Matière première' => Asset::TYPE_COMMODITY,
                    'Devise' => Asset::TYPE_CURRENCY,
                ],
                'attr' => [
                    'class' => 'form-select modern-select',
                    'data-bs-toggle' => 'tooltip',
                    'data-bs-placement' => 'top',
                    'title' => 'Sélectionnez le type d\'actif'
                ],
                'constraints' => [
                    new Assert\NotBlank(['message' => 'Le type d\'actif est obligatoire'])
                ]
            ])
            ->add('category', ChoiceType::class, [
                'label' => 'Catégorie',
                'choices' => [
                    'Crypto majeure' => Asset::CATEGORY_CRYPTO_MAJOR,
                    'Altcoin' => Asset::CATEGORY_CRYPTO_ALT,
                    'Action française' => Asset::CATEGORY_STOCK_FRENCH,
                    'Action internationale' => Asset::CATEGORY_STOCK_INTERNATIONAL,
                    'ETF actions' => Asset::CATEGORY_ETF_EQUITY,
                    'ETF obligations' => Asset::CATEGORY_ETF_BOND,
                    'Épargne réglementée' => Asset::CATEGORY_SAVINGS_REGULATED,
                    'Épargne à terme' => Asset::CATEGORY_SAVINGS_TERM,
                ],
                'required' => false,
                'placeholder' => 'Sélectionnez une catégorie (optionnel)',
                'attr' => [
                    'class' => 'form-select modern-select',
                    'data-bs-toggle' => 'tooltip',
                    'data-bs-placement' => 'top',
                    'title' => 'Catégorie plus spécifique de l\'actif'
                ]
            ])
            ->add('currency', ChoiceType::class, [
                'label' => 'Devise',
                'choices' => [
                    'EUR (Euro)' => 'EUR',
                    'USD (Dollar américain)' => 'USD',
                    'GBP (Livre sterling)' => 'GBP',
                    'CHF (Franc suisse)' => 'CHF',
                    'JPY (Yen japonais)' => 'JPY',
                    'CAD (Dollar canadien)' => 'CAD',
                    'AUD (Dollar australien)' => 'AUD',
                ],
                'required' => false,
                'placeholder' => 'Sélectionnez une devise (optionnel)',
                'attr' => [
                    'class' => 'form-select modern-select',
                    'data-bs-toggle' => 'tooltip',
                    'data-bs-placement' => 'top',
                    'title' => 'Devise principale de l\'actif'
                ]
            ])
            ->add('currentPrice', NumberType::class, [
                'label' => 'Prix actuel',
                'required' => false,
                'scale' => 8,
                'attr' => [
                    'class' => 'form-control modern-input',
                    'placeholder' => '0.00000000',
                    'step' => '0.00000001',
                    'data-bs-toggle' => 'tooltip',
                    'data-bs-placement' => 'top',
                    'title' => 'Prix actuel de l\'actif (sera mis à jour automatiquement)'
                ]
            ])
            ->add('exchange', TextType::class, [
                'label' => 'Plateforme d\'échange',
                'required' => false,
                'attr' => [
                    'class' => 'form-control modern-input',
                    'placeholder' => 'Ex: Binance, NYSE, Euronext',
                    'data-bs-toggle' => 'tooltip',
                    'data-bs-placement' => 'top',
                    'title' => 'Plateforme où l\'actif est tradé'
                ]
            ])
            ->add('isin', TextType::class, [
                'label' => 'Code ISIN',
                'required' => false,
                'attr' => [
                    'class' => 'form-control modern-input',
                    'placeholder' => 'Ex: US0378331005',
                    'data-bs-toggle' => 'tooltip',
                    'data-bs-placement' => 'top',
                    'title' => 'Code ISIN pour les titres financiers'
                ],
                'constraints' => [
                    new Assert\Length([
                        'max' => 12,
                        'maxMessage' => 'Le code ISIN ne peut pas dépasser {{ limit }} caractères'
                    ])
                ]
            ])
            ->add('description', TextareaType::class, [
                'label' => 'Description',
                'required' => false,
                'attr' => [
                    'class' => 'form-control modern-textarea',
                    'placeholder' => 'Description optionnelle de l\'actif...',
                    'rows' => 3,
                    'data-bs-toggle' => 'tooltip',
                    'data-bs-placement' => 'top',
                    'title' => 'Description détaillée de l\'actif'
                ]
            ])
            ->add('isActive', CheckboxType::class, [
                'label' => 'Actif',
                'required' => false,
                'attr' => [
                    'class' => 'form-check-input modern-checkbox',
                    'data-bs-toggle' => 'tooltip',
                    'data-bs-placement' => 'top',
                    'title' => 'Cocher si l\'actif est actuellement suivi'
                ]
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Asset::class,
        ]);
    }
}
