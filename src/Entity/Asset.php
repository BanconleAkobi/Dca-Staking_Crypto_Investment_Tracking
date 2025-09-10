<?php

namespace App\Entity;

use App\Repository\AssetRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: AssetRepository::class)]
class Asset
{
    // Types d'actifs supportés
    public const TYPE_CRYPTO = 'crypto';
    public const TYPE_STOCK = 'stock';
    public const TYPE_ETF = 'etf';
    public const TYPE_BOND = 'bond';
    public const TYPE_SAVINGS = 'savings';
    public const TYPE_REAL_ESTATE = 'real_estate';
    public const TYPE_COMMODITY = 'commodity';
    public const TYPE_CURRENCY = 'currency';

    // Catégories pour les sous-types
    public const CATEGORY_CRYPTO_MAJOR = 'crypto_major';
    public const CATEGORY_CRYPTO_ALT = 'crypto_alt';
    public const CATEGORY_STOCK_FRENCH = 'stock_french';
    public const CATEGORY_STOCK_INTERNATIONAL = 'stock_international';
    public const CATEGORY_ETF_EQUITY = 'etf_equity';
    public const CATEGORY_ETF_BOND = 'etf_bond';
    public const CATEGORY_SAVINGS_REGULATED = 'savings_regulated';
    public const CATEGORY_SAVINGS_TERM = 'savings_term';

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column(length: 10)]
    private ?string $symbol = null;

    #[ORM\Column(length: 50)]
    private ?string $type = null;

    #[ORM\Column(length: 50, nullable: true)]
    private ?string $category = null;

    #[ORM\Column(length: 3, nullable: true)]
    private ?string $currency = null;

    #[ORM\Column(nullable: true)]
    private ?float $currentPrice = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $lastPriceUpdate = null;

    #[ORM\Column(nullable: true)]
    private ?string $exchange = null;

    #[ORM\Column(nullable: true)]
    private ?string $isin = null;

    #[ORM\Column(nullable: true)]
    private ?string $description = null;

    #[ORM\Column(nullable: true)]
    private ?bool $isActive = true;

    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $updatedAt = null;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'assets')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $user = null;

    /**
     * @var Collection<int, Transaction>
     */
    #[ORM\OneToMany(targetEntity: Transaction::class, mappedBy: 'asset')]
    private Collection $transactions;

    /**
     * @var Collection<int, Withdrawal>
     */
    #[ORM\OneToMany(targetEntity: Withdrawal::class, mappedBy: 'asset')]
    private Collection $withdrawals;

    public function __construct()
    {
        $this->transactions = new ArrayCollection();
        $this->withdrawals = new ArrayCollection();
        $this->createdAt = new \DateTimeImmutable();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;
        return $this;
    }

    public function getSymbol(): ?string
    {
        return $this->symbol;
    }

    public function setSymbol(string $symbol): static
    {
        $this->symbol = $symbol;
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

    public function getCategory(): ?string
    {
        return $this->category;
    }

    public function setCategory(?string $category): static
    {
        $this->category = $category;
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

    public function getCurrentPrice(): ?float
    {
        return $this->currentPrice;
    }

    public function setCurrentPrice(?float $currentPrice): static
    {
        $this->currentPrice = $currentPrice;
        return $this;
    }

    public function getLastPriceUpdate(): ?\DateTimeImmutable
    {
        return $this->lastPriceUpdate;
    }

    public function setLastPriceUpdate(?\DateTimeImmutable $lastPriceUpdate): static
    {
        $this->lastPriceUpdate = $lastPriceUpdate;
        return $this;
    }

    public function getExchange(): ?string
    {
        return $this->exchange;
    }

    public function setExchange(?string $exchange): static
    {
        $this->exchange = $exchange;
        return $this;
    }

    public function getIsin(): ?string
    {
        return $this->isin;
    }

    public function setIsin(?string $isin): static
    {
        $this->isin = $isin;
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

    public function isActive(): ?bool
    {
        return $this->isActive;
    }

    public function setIsActive(?bool $isActive): static
    {
        $this->isActive = $isActive;
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

    /**
     * @return Collection<int, Transaction>
     */
    public function getTransactions(): Collection
    {
        return $this->transactions;
    }

    public function addTransaction(Transaction $transaction): static
    {
        if (!$this->transactions->contains($transaction)) {
            $this->transactions->add($transaction);
            $transaction->setAsset($this);
        }
        return $this;
    }

    public function removeTransaction(Transaction $transaction): static
    {
        if ($this->transactions->removeElement($transaction)) {
            if ($transaction->getAsset() === $this) {
                $transaction->setAsset(null);
            }
        }
        return $this;
    }

    /**
     * @return Collection<int, Withdrawal>
     */
    public function getWithdrawals(): Collection
    {
        return $this->withdrawals;
    }

    public function addWithdrawal(Withdrawal $withdrawal): static
    {
        if (!$this->withdrawals->contains($withdrawal)) {
            $this->withdrawals->add($withdrawal);
            $withdrawal->setAsset($this);
        }
        return $this;
    }

    public function removeWithdrawal(Withdrawal $withdrawal): static
    {
        if ($this->withdrawals->removeElement($withdrawal)) {
            if ($withdrawal->getAsset() === $this) {
                $withdrawal->setAsset(null);
            }
        }
        return $this;
    }

    // Méthodes utilitaires
    public function getTypeLabel(): string
    {
        return match($this->type) {
            self::TYPE_CRYPTO => 'Cryptomonnaie',
            self::TYPE_STOCK => 'Action',
            self::TYPE_ETF => 'ETF',
            self::TYPE_BOND => 'Obligation',
            self::TYPE_SAVINGS => 'Épargne',
            self::TYPE_REAL_ESTATE => 'Immobilier',
            self::TYPE_COMMODITY => 'Matière première',
            self::TYPE_CURRENCY => 'Devise',
            default => 'Actif'
        };
    }

    public function getCategoryLabel(): string
    {
        return match($this->category) {
            self::CATEGORY_CRYPTO_MAJOR => 'Crypto majeure',
            self::CATEGORY_CRYPTO_ALT => 'Altcoin',
            self::CATEGORY_STOCK_FRENCH => 'Action française',
            self::CATEGORY_STOCK_INTERNATIONAL => 'Action internationale',
            self::CATEGORY_ETF_EQUITY => 'ETF actions',
            self::CATEGORY_ETF_BOND => 'ETF obligations',
            self::CATEGORY_SAVINGS_REGULATED => 'Épargne réglementée',
            self::CATEGORY_SAVINGS_TERM => 'Épargne à terme',
            default => 'Autre'
        };
    }

    public function getDisplayName(): string
    {
        return $this->name . ' (' . $this->symbol . ')';
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
}
