<?php

namespace App\Entity;

use App\Repository\WithdrawalRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: WithdrawalRepository::class)]
class Withdrawal
{
    // Types de retraits
    public const TYPE_RETIREMENT = 'retirement';
    public const TYPE_EMERGENCY = 'emergency';
    public const TYPE_REBALANCING = 'rebalancing';
    public const TYPE_TAX_PAYMENT = 'tax_payment';
    public const TYPE_PURCHASE = 'purchase';
    public const TYPE_INVESTMENT = 'investment';
    public const TYPE_OTHER = 'other';

    // Statuts de retrait
    public const STATUS_PENDING = 'pending';
    public const STATUS_COMPLETED = 'completed';
    public const STATUS_CANCELLED = 'cancelled';

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?float $amount = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $date = null;

    #[ORM\Column(length: 50)]
    private ?string $type = null;

    #[ORM\Column(length: 50)]
    private ?string $status = null;

    #[ORM\Column(nullable: true)]
    private ?string $reason = null;

    #[ORM\Column(nullable: true)]
    private ?float $taxAmount = null;

    #[ORM\Column(nullable: true)]
    private ?float $fees = null;

    #[ORM\Column(nullable: true)]
    private ?string $description = null;

    #[ORM\Column(nullable: true)]
    private ?string $reference = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $updatedAt = null;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'withdrawals')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $user = null;

    #[ORM\ManyToOne(targetEntity: Asset::class, inversedBy: 'withdrawals')]
    #[ORM\JoinColumn(nullable: true)]
    private ?Asset $asset = null;

    #[ORM\ManyToOne(targetEntity: SavingsAccount::class, inversedBy: 'withdrawals')]
    #[ORM\JoinColumn(nullable: true)]
    private ?SavingsAccount $savingsAccount = null;

    public function __construct()
    {
        $this->createdAt = new \DateTimeImmutable();
        $this->updatedAt = new \DateTimeImmutable();
        $this->status = self::STATUS_PENDING;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getAmount(): ?float
    {
        return $this->amount;
    }

    public function setAmount(float $amount): static
    {
        $this->amount = $amount;
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

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(string $type): static
    {
        $this->type = $type;
        return $this;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(string $status): static
    {
        $this->status = $status;
        return $this;
    }

    public function getReason(): ?string
    {
        return $this->reason;
    }

    public function setReason(?string $reason): static
    {
        $this->reason = $reason;
        return $this;
    }

    public function getTaxAmount(): ?float
    {
        return $this->taxAmount;
    }

    public function setTaxAmount(?float $taxAmount): static
    {
        $this->taxAmount = $taxAmount;
        return $this;
    }

    public function getFees(): ?float
    {
        return $this->fees;
    }

    public function setFees(?float $fees): static
    {
        $this->fees = $fees;
        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): static
    {
        $this->description = $description;
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

    // Méthodes utilitaires
    public function getTypeLabel(): string
    {
        return match($this->type) {
            self::TYPE_RETIREMENT => 'Retraite',
            self::TYPE_EMERGENCY => 'Urgence',
            self::TYPE_REBALANCING => 'Rééquilibrage',
            self::TYPE_TAX_PAYMENT => 'Paiement d\'impôts',
            self::TYPE_PURCHASE => 'Achat',
            self::TYPE_INVESTMENT => 'Réinvestissement',
            self::TYPE_OTHER => 'Autre',
            default => 'Retrait'
        };
    }

    public function getStatusLabel(): string
    {
        return match($this->status) {
            self::STATUS_PENDING => 'En attente',
            self::STATUS_COMPLETED => 'Terminé',
            self::STATUS_CANCELLED => 'Annulé',
            default => 'Inconnu'
        };
    }

    public function getStatusColor(): string
    {
        return match($this->status) {
            self::STATUS_PENDING => 'warning',
            self::STATUS_COMPLETED => 'success',
            self::STATUS_CANCELLED => 'danger',
            default => 'secondary'
        };
    }

    public function getNetAmount(): float
    {
        $netAmount = $this->amount;
        if ($this->taxAmount) {
            $netAmount -= $this->taxAmount;
        }
        if ($this->fees) {
            $netAmount -= $this->fees;
        }
        return $netAmount;
    }

    public function getSourceName(): string
    {
        if ($this->asset) {
            return $this->asset->getDisplayName();
        }
        if ($this->savingsAccount) {
            return $this->savingsAccount->getDisplayName();
        }
        return 'Source inconnue';
    }

    public function isCompleted(): bool
    {
        return $this->status === self::STATUS_COMPLETED;
    }

    public function isPending(): bool
    {
        return $this->status === self::STATUS_PENDING;
    }

    public function isCancelled(): bool
    {
        return $this->status === self::STATUS_CANCELLED;
    }
}
