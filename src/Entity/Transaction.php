<?php

namespace App\Entity;

use App\Repository\TransactionRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: TransactionRepository::class)]
class Transaction
{
    // Types de transactions
    public const TYPE_BUY = 'BUY';
    public const TYPE_SELL = 'SELL';
    public const TYPE_DIVIDEND = 'DIVIDEND';
    public const TYPE_INTEREST = 'INTEREST';
    public const TYPE_STAKE_REWARD = 'STAKE_REWARD';
    public const TYPE_DEPOSIT = 'DEPOSIT';
    public const TYPE_WITHDRAWAL = 'WITHDRAWAL';
    public const TYPE_TRANSFER = 'TRANSFER';
    public const TYPE_REINVESTMENT = 'REINVESTMENT';
    public const TYPE_TAX = 'TAX';
    public const TYPE_FEE = 'FEE';

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'transactions')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $user = null;

    #[ORM\ManyToOne(inversedBy: 'transactions')]
    #[ORM\JoinColumn(nullable: true)]
    private ?Asset $asset = null;

    #[ORM\ManyToOne(inversedBy: 'transactions')]
    #[ORM\JoinColumn(nullable: true)]
    private ?SavingsAccount $savingsAccount = null;

    // Garder la relation crypto pour la compatibilité pendant la migration
    #[ORM\ManyToOne(inversedBy: 'transactions')]
    #[ORM\JoinColumn(nullable: true)]
    private ?Crypto $crypto = null;

    #[ORM\Column(length: 20)]
    #[Assert\Choice(choices: [
        self::TYPE_BUY, self::TYPE_SELL, self::TYPE_DIVIDEND, self::TYPE_INTEREST,
        self::TYPE_STAKE_REWARD, self::TYPE_DEPOSIT, self::TYPE_WITHDRAWAL,
        self::TYPE_TRANSFER, self::TYPE_REINVESTMENT, self::TYPE_TAX, self::TYPE_FEE
    ])]
    private ?string $type = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $date = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 0, nullable: true)]
    private ?string $unit_price_usd = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 0)]
    private ?string $quantity = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 0, nullable: true)]
    private ?string $fee_usd = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $note = null;

    #[ORM\Column(length: 3, nullable: true)]
    private ?string $currency = null;

    #[ORM\Column(nullable: true)]
    private ?float $exchangeRate = null;

    #[ORM\Column(nullable: true)]
    private ?string $reference = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $updatedAt = null;

    public function __construct()
    {
        $this->createdAt = new \DateTimeImmutable();
        $this->updatedAt = new \DateTimeImmutable();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): static
    {
        $this->user = $user;
        return $this;
    }

    public function getAsset(): ?Asset
    {
        return $this->asset;
    }

    public function setAsset(?Asset $asset): static
    {
        $this->asset = $asset;
        return $this;
    }

    public function getSavingsAccount(): ?SavingsAccount
    {
        return $this->savingsAccount;
    }

    public function setSavingsAccount(?SavingsAccount $savingsAccount): static
    {
        $this->savingsAccount = $savingsAccount;
        return $this;
    }

    public function getCrypto(): ?Crypto
    {
        return $this->crypto;
    }

    public function setCrypto(?Crypto $crypto): static
    {
        $this->crypto = $crypto;
        return $this;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(string $type): static
    {
        $this->type = $type;

        return $this;
    }

    public function getDate(): ?\DateTimeImmutable
    {
        return $this->date;
    }

    public function setDate(\DateTimeImmutable $date): static
    {
        $this->date = $date;

        return $this;
    }

    public function getUnitPriceUsd(): ?string
    {
        return $this->unit_price_usd;
    }

    public function setUnitPriceUsd(?string $unit_price_usd): static
    {
        $this->unit_price_usd = $unit_price_usd;

        return $this;
    }

    public function getQuantity(): ?string
    {
        return $this->quantity;
    }

    public function setQuantity(string $quantity): static
    {
        $this->quantity = $quantity;

        return $this;
    }

    public function getFeeUsd(): ?string
    {
        return $this->fee_usd;
    }

    public function setFeeUsd(?string $fee_usd): static
    {
        $this->fee_usd = $fee_usd;

        return $this;
    }

    public function getNote(): ?string
    {
        return $this->note;
    }

    public function setNote(?string $note): static
    {
        $this->note = $note;
        return $this;
    }

    public function getCurrency(): ?string
    {
        return $this->currency;
    }

    public function setCurrency(?string $currency): static
    {
        $this->currency = $currency;
        return $this;
    }

    public function getExchangeRate(): ?float
    {
        return $this->exchangeRate;
    }

    public function setExchangeRate(?float $exchangeRate): static
    {
        $this->exchangeRate = $exchangeRate;
        return $this;
    }

    public function getReference(): ?string
    {
        return $this->reference;
    }

    public function setReference(?string $reference): static
    {
        $this->reference = $reference;
        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(?\DateTimeImmutable $createdAt): static
    {
        $this->createdAt = $createdAt;
        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeImmutable
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(?\DateTimeImmutable $updatedAt): static
    {
        $this->updatedAt = $updatedAt;
        return $this;
    }

    // Méthodes utilitaires
    public function getTypeLabel(): string
    {
        return match($this->type) {
            self::TYPE_BUY => 'Achat',
            self::TYPE_SELL => 'Vente',
            self::TYPE_DIVIDEND => 'Dividende',
            self::TYPE_INTEREST => 'Intérêts',
            self::TYPE_STAKE_REWARD => 'Récompense de staking',
            self::TYPE_DEPOSIT => 'Dépôt',
            self::TYPE_WITHDRAWAL => 'Retrait',
            self::TYPE_TRANSFER => 'Transfert',
            self::TYPE_REINVESTMENT => 'Réinvestissement',
            self::TYPE_TAX => 'Impôt',
            self::TYPE_FEE => 'Frais',
            default => 'Transaction'
        };
    }

    public function getSourceName(): string
    {
        if ($this->asset) {
            return $this->asset->getDisplayName();
        }
        if ($this->savingsAccount) {
            return $this->savingsAccount->getDisplayName();
        }
        if ($this->crypto) {
            return $this->crypto->getDisplayName();
        }
        return 'Source inconnue';
    }

    public function getTotalAmount(): float
    {
        $quantity = (float) $this->quantity;
        $unitPrice = (float) $this->unit_price_usd;
        $fee = (float) $this->fee_usd;
        
        return ($quantity * $unitPrice) + $fee;
    }

    public function getNetAmount(): float
    {
        $quantity = (float) $this->quantity;
        $unitPrice = (float) $this->unit_price_usd;
        $fee = (float) $this->fee_usd;
        
        return ($quantity * $unitPrice) - $fee;
    }

    public function isBuy(): bool
    {
        return $this->type === self::TYPE_BUY;
    }

    public function isSell(): bool
    {
        return $this->type === self::TYPE_SELL;
    }

    public function isIncome(): bool
    {
        return in_array($this->type, [
            self::TYPE_DIVIDEND,
            self::TYPE_INTEREST,
            self::TYPE_STAKE_REWARD
        ]);
    }
}
