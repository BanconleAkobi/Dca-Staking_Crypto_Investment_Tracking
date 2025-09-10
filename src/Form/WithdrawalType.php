<?php

namespace App\Form;

use App\Entity\Withdrawal;
use App\Entity\Asset;
use App\Entity\SavingsAccount;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;

class WithdrawalType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('amount', NumberType::class, [
                'label' => 'Montant du retrait',
                'scale' => 2,
                'attr' => [
                    'class' => 'form-control modern-input',
                    'placeholder' => '0.00',
                    'step' => '0.01',
                    'data-bs-toggle' => 'tooltip',
                    'data-bs-placement' => 'top',
                    'title' => 'Montant du retrait en euros'
                ],
                'constraints' => [
                    new Assert\NotBlank(['message' => 'Le montant est obligatoire']),
                    new Assert\Positive(['message' => 'Le montant doit être positif'])
                ]
            ])
            ->add('date', DateType::class, [
                'label' => 'Date du retrait',
                'widget' => 'single_text',
                'attr' => [
                    'class' => 'form-control modern-date',
                    'data-bs-toggle' => 'tooltip',
                    'data-bs-placement' => 'top',
                    'title' => 'Date à laquelle le retrait a été effectué'
                ],
                'constraints' => [
                    new Assert\NotBlank(['message' => 'La date est obligatoire'])
                ]
            ])
            ->add('type', ChoiceType::class, [
                'label' => 'Type de retrait',
                'choices' => [
                    'Retraite' => Withdrawal::TYPE_RETIREMENT,
                    'Urgence' => Withdrawal::TYPE_EMERGENCY,
                    'Rééquilibrage' => Withdrawal::TYPE_REBALANCING,
                    'Paiement d\'impôts' => Withdrawal::TYPE_TAX_PAYMENT,
                    'Achat' => Withdrawal::TYPE_PURCHASE,
                    'Réinvestissement' => Withdrawal::TYPE_INVESTMENT,
                    'Autre' => Withdrawal::TYPE_OTHER,
                ],
                'attr' => [
                    'class' => 'form-select modern-select',
                    'data-bs-toggle' => 'tooltip',
                    'data-bs-placement' => 'top',
                    'title' => 'Raison du retrait'
                ],
                'constraints' => [
                    new Assert\NotBlank(['message' => 'Le type de retrait est obligatoire'])
                ]
            ])
            ->add('status', ChoiceType::class, [
                'label' => 'Statut',
                'choices' => [
                    'En attente' => Withdrawal::STATUS_PENDING,
                    'Terminé' => Withdrawal::STATUS_COMPLETED,
                    'Annulé' => Withdrawal::STATUS_CANCELLED,
                ],
                'attr' => [
                    'class' => 'form-select modern-select',
                    'data-bs-toggle' => 'tooltip',
                    'data-bs-placement' => 'top',
                    'title' => 'Statut actuel du retrait'
                ],
                'constraints' => [
                    new Assert\NotBlank(['message' => 'Le statut est obligatoire'])
                ]
            ])
            ->add('asset', EntityType::class, [
                'label' => 'Actif source',
                'class' => Asset::class,
                'choice_label' => 'displayName',
                'required' => false,
                'placeholder' => 'Sélectionnez un actif (optionnel)',
                'attr' => [
                    'class' => 'form-select modern-select',
                    'data-bs-toggle' => 'tooltip',
                    'data-bs-placement' => 'top',
                    'title' => 'Actif depuis lequel le retrait a été effectué'
                ]
            ])
            ->add('savingsAccount', EntityType::class, [
                'label' => 'Compte d\'épargne source',
                'class' => SavingsAccount::class,
                'choice_label' => 'displayName',
                'required' => false,
                'placeholder' => 'Sélectionnez un compte d\'épargne (optionnel)',
                'attr' => [
                    'class' => 'form-select modern-select',
                    'data-bs-toggle' => 'tooltip',
                    'data-bs-placement' => 'top',
                    'title' => 'Compte d\'épargne depuis lequel le retrait a été effectué'
                ]
            ])
            ->add('reason', TextType::class, [
                'label' => 'Raison détaillée',
                'required' => false,
                'attr' => [
                    'class' => 'form-control modern-input',
                    'placeholder' => 'Ex: Achat d\'une voiture, Urgence médicale',
                    'data-bs-toggle' => 'tooltip',
                    'data-bs-placement' => 'top',
                    'title' => 'Raison détaillée du retrait'
                ]
            ])
            ->add('taxAmount', NumberType::class, [
                'label' => 'Montant des impôts',
                'required' => false,
                'scale' => 2,
                'attr' => [
                    'class' => 'form-control modern-input',
                    'placeholder' => '0.00',
                    'step' => '0.01',
                    'data-bs-toggle' => 'tooltip',
                    'data-bs-placement' => 'top',
                    'title' => 'Montant des impôts payés sur ce retrait'
                ]
            ])
            ->add('fees', NumberType::class, [
                'label' => 'Frais',
                'required' => false,
                'scale' => 2,
                'attr' => [
                    'class' => 'form-control modern-input',
                    'placeholder' => '0.00',
                    'step' => '0.01',
                    'data-bs-toggle' => 'tooltip',
                    'data-bs-placement' => 'top',
                    'title' => 'Frais associés au retrait'
                ]
            ])
            ->add('reference', TextType::class, [
                'label' => 'Référence',
                'required' => false,
                'attr' => [
                    'class' => 'form-control modern-input',
                    'placeholder' => 'Ex: VIR-2024-001, CHQ-123456',
                    'data-bs-toggle' => 'tooltip',
                    'data-bs-placement' => 'top',
                    'title' => 'Référence de la transaction bancaire'
                ]
            ])
            ->add('description', TextareaType::class, [
                'label' => 'Description',
                'required' => false,
                'attr' => [
                    'class' => 'form-control modern-textarea',
                    'placeholder' => 'Description optionnelle du retrait...',
                    'rows' => 3,
                    'data-bs-toggle' => 'tooltip',
                    'data-bs-placement' => 'top',
                    'title' => 'Description détaillée du retrait'
                ]
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Withdrawal::class,
        ]);
    }
}
