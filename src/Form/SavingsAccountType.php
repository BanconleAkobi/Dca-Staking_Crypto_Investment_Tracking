<?php

namespace App\Form;

use App\Entity\SavingsAccount;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;

class SavingsAccountType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'label' => 'Nom du compte',
                'attr' => [
                    'class' => 'form-control modern-input',
                    'placeholder' => 'Ex: Mon Livret A, PEL Maison',
                    'data-bs-toggle' => 'tooltip',
                    'data-bs-placement' => 'top',
                    'title' => 'Nom personnalisé pour identifier ce compte'
                ],
                'constraints' => [
                    new Assert\NotBlank(['message' => 'Le nom du compte est obligatoire']),
                    new Assert\Length([
                        'min' => 2,
                        'max' => 255,
                        'minMessage' => 'Le nom doit contenir au moins {{ limit }} caractères',
                        'maxMessage' => 'Le nom ne peut pas dépasser {{ limit }} caractères'
                    ])
                ]
            ])
            ->add('type', ChoiceType::class, [
                'label' => 'Type de compte',
                'choices' => [
                    // Épargne réglementée
                    'Livret A' => SavingsAccount::TYPE_LIVRET_A,
                    'LDDS (Livret de développement durable)' => SavingsAccount::TYPE_LDDS,
                    'LEP (Livret d\'épargne populaire)' => SavingsAccount::TYPE_LEP,
                    'PEL (Plan épargne logement)' => SavingsAccount::TYPE_PEL,
                    'CEL (Compte épargne logement)' => SavingsAccount::TYPE_CEL,
                    'Livret A Jeune' => SavingsAccount::TYPE_LAJ,
                    'LDD (Livret de développement durable)' => SavingsAccount::TYPE_LDD,
                    'LEP+' => SavingsAccount::TYPE_LEP_PLUS,
                    
                    // Épargne à terme
                    'Dépôt à terme' => SavingsAccount::TYPE_TERM_DEPOSIT,
                    'Obligation d\'épargne' => SavingsAccount::TYPE_SAVINGS_BOND,
                    'Assurance vie' => SavingsAccount::TYPE_ASSURANCE_VIE,
                    'PEA (Plan d\'épargne en actions)' => SavingsAccount::TYPE_PEA,
                    'PEA-PME' => SavingsAccount::TYPE_PEA_PME,
                ],
                'attr' => [
                    'class' => 'form-select modern-select',
                    'data-bs-toggle' => 'tooltip',
                    'data-bs-placement' => 'top',
                    'title' => 'Sélectionnez le type de compte d\'épargne'
                ],
                'constraints' => [
                    new Assert\NotBlank(['message' => 'Le type de compte est obligatoire'])
                ]
            ])
            ->add('bankName', TextType::class, [
                'label' => 'Nom de la banque',
                'required' => false,
                'attr' => [
                    'class' => 'form-control modern-input',
                    'placeholder' => 'Ex: Crédit Agricole, BNP Paribas, La Banque Postale',
                    'data-bs-toggle' => 'tooltip',
                    'data-bs-placement' => 'top',
                    'title' => 'Nom de l\'établissement bancaire'
                ]
            ])
            ->add('accountNumber', TextType::class, [
                'label' => 'Numéro de compte',
                'required' => false,
                'attr' => [
                    'class' => 'form-control modern-input',
                    'placeholder' => 'Numéro de compte (optionnel)',
                    'data-bs-toggle' => 'tooltip',
                    'data-bs-placement' => 'top',
                    'title' => 'Numéro de compte bancaire (optionnel)'
                ]
            ])
            ->add('currentBalance', NumberType::class, [
                'label' => 'Solde actuel',
                'required' => false,
                'scale' => 2,
                'attr' => [
                    'class' => 'form-control modern-input',
                    'placeholder' => '0.00',
                    'step' => '0.01',
                    'data-bs-toggle' => 'tooltip',
                    'data-bs-placement' => 'top',
                    'title' => 'Solde actuel du compte en euros'
                ]
            ])
            ->add('annualRate', NumberType::class, [
                'label' => 'Taux annuel (%)',
                'required' => false,
                'scale' => 3,
                'attr' => [
                    'class' => 'form-control modern-input',
                    'placeholder' => '0.000',
                    'step' => '0.001',
                    'data-bs-toggle' => 'tooltip',
                    'data-bs-placement' => 'top',
                    'title' => 'Taux d\'intérêt annuel en pourcentage'
                ]
            ])
            ->add('maxAmount', NumberType::class, [
                'label' => 'Plafond (€)',
                'required' => false,
                'scale' => 2,
                'attr' => [
                    'class' => 'form-control modern-input',
                    'placeholder' => '0.00',
                    'step' => '0.01',
                    'data-bs-toggle' => 'tooltip',
                    'data-bs-placement' => 'top',
                    'title' => 'Plafond maximum autorisé pour ce type de compte'
                ]
            ])
            ->add('openingDate', DateType::class, [
                'label' => 'Date d\'ouverture',
                'required' => false,
                'widget' => 'single_text',
                'attr' => [
                    'class' => 'form-control modern-date',
                    'data-bs-toggle' => 'tooltip',
                    'data-bs-placement' => 'top',
                    'title' => 'Date d\'ouverture du compte'
                ]
            ])
            ->add('maturityDate', DateType::class, [
                'label' => 'Date d\'échéance',
                'required' => false,
                'widget' => 'single_text',
                'attr' => [
                    'class' => 'form-control modern-date',
                    'data-bs-toggle' => 'tooltip',
                    'data-bs-placement' => 'top',
                    'title' => 'Date d\'échéance (pour les comptes à terme)'
                ]
            ])
            ->add('isTaxFree', CheckboxType::class, [
                'label' => 'Exonéré d\'impôts',
                'required' => false,
                'attr' => [
                    'class' => 'form-check-input modern-checkbox',
                    'data-bs-toggle' => 'tooltip',
                    'data-bs-placement' => 'top',
                    'title' => 'Cocher si les intérêts sont exonérés d\'impôts'
                ]
            ])
            ->add('taxRate', NumberType::class, [
                'label' => 'Taux d\'imposition (%)',
                'required' => false,
                'scale' => 2,
                'attr' => [
                    'class' => 'form-control modern-input',
                    'placeholder' => '0.00',
                    'step' => '0.01',
                    'data-bs-toggle' => 'tooltip',
                    'data-bs-placement' => 'top',
                    'title' => 'Taux d\'imposition sur les intérêts'
                ]
            ])
            ->add('description', TextareaType::class, [
                'label' => 'Description',
                'required' => false,
                'attr' => [
                    'class' => 'form-control modern-textarea',
                    'placeholder' => 'Description optionnelle du compte...',
                    'rows' => 3,
                    'data-bs-toggle' => 'tooltip',
                    'data-bs-placement' => 'top',
                    'title' => 'Description détaillée du compte'
                ]
            ])
            ->add('isActive', CheckboxType::class, [
                'label' => 'Actif',
                'required' => false,
                'attr' => [
                    'class' => 'form-check-input modern-checkbox',
                    'data-bs-toggle' => 'tooltip',
                    'data-bs-placement' => 'top',
                    'title' => 'Cocher si le compte est actuellement suivi'
                ]
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => SavingsAccount::class,
        ]);
    }
}
