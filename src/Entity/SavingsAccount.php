<?php

namespace App\Entity;

use App\Repository\SavingsAccountRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: SavingsAccountRepository::class)]
class SavingsAccount
{
    // Types de livrets français
    public const TYPE_LIVRET_A = 'livret_a';
    public const TYPE_LDDS = 'ldds';
    public const TYPE_LEP = 'lep';
    public const TYPE_PEL = 'pel';
    public const TYPE_CEL = 'cel';
    public const TYPE_LAJ = 'laj'; // Livret A Jeune
    public const TYPE_LDD = 'ldd'; // Livret de développement durable
    public const TYPE_LEP_PLUS = 'lep_plus';

    // Types d'épargne à terme
    public const TYPE_TERM_DEPOSIT = 'term_deposit';
    public const TYPE_SAVINGS_BOND = 'savings_bond';
    public const TYPE_ASSURANCE_VIE = 'assurance_vie';
    public const TYPE_PEA = 'pea';
    public const TYPE_PEA_PME = 'pea_pme';

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column(length: 50)]
    private ?string $type = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $bankName = null;

    #[ORM\Column(length: 50, nullable: true)]
    private ?string $accountNumber = null;

    #[ORM\Column(nullable: true)]
    private ?float $currentBalance = null;

    #[ORM\Column(nullable: true)]
    private ?float $annualRate = null;

    #[ORM\Column(nullable: true)]
    private ?float $maxAmount = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $openingDate = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $maturityDate = null;

    #[ORM\Column(nullable: true)]
    private ?bool $isTaxFree = null;

    #[ORM\Column(nullable: true)]
    private ?float $taxRate = null;

    #[ORM\Column(nullable: true)]
    private ?bool $isActive = true;

    #[ORM\Column(nullable: true)]
    private ?string $description = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $updatedAt = null;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'savingsAccounts')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $user = null;

    /**
     * @var Collection<int, Transaction>
     */
    #[ORM\OneToMany(targetEntity: Transaction::class, mappedBy: 'savingsAccount')]
    private Collection $transactions;

    public function __construct()
    {
        $this->transactions = new ArrayCollection();
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

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(string $type): static
    {
        $this->type = $type;
        return $this;
    }

    public function getBankName(): ?string
    {
        return $this->bankName;
    }

    public function setBankName(?string $bankName): static
    {
        $this->bankName = $bankName;
        return $this;
    }

    public function getAccountNumber(): ?string
    {
        return $this->accountNumber;
    }

    public function setAccountNumber(?string $accountNumber): static
    {
        $this->accountNumber = $accountNumber;
        return $this;
    }

    public function getCurrentBalance(): ?float
    {
        return $this->currentBalance;
    }

    public function setCurrentBalance(?float $currentBalance): static
    {
        $this->currentBalance = $currentBalance;
        return $this;
    }

    public function getAnnualRate(): ?float
    {
        return $this->annualRate;
    }

    public function setAnnualRate(?float $annualRate): static
    {
        $this->annualRate = $annualRate;
        return $this;
    }

    public function getMaxAmount(): ?float
    {
        return $this->maxAmount;
    }

    public function setMaxAmount(?float $maxAmount): static
    {
        $this->maxAmount = $maxAmount;
        return $this;
    }

    public function getOpeningDate(): ?\DateTimeImmutable
    {
        return $this->openingDate;
    }

    public function setOpeningDate(?\DateTimeImmutable $openingDate): static
    {
        $this->openingDate = $openingDate;
        return $this;
    }

    public function getMaturityDate(): ?\DateTimeImmutable
    {
        return $this->maturityDate;
    }

    public function setMaturityDate(?\DateTimeImmutable $maturityDate): static
    {
        $this->maturityDate = $maturityDate;
        return $this;
    }

    public function isTaxFree(): ?bool
    {
        return $this->isTaxFree;
    }

    public function setIsTaxFree(?bool $isTaxFree): static
    {
        $this->isTaxFree = $isTaxFree;
        return $this;
    }

    public function getTaxRate(): ?float
    {
        return $this->taxRate;
    }

    public function setTaxRate(?float $taxRate): static
    {
        $this->taxRate = $taxRate;
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

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): static
    {
        $this->description = $description;
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
            $transaction->setSavingsAccount($this);
        }
        return $this;
    }

    public function removeTransaction(Transaction $transaction): static
    {
        if ($this->transactions->removeElement($transaction)) {
            if ($transaction->getSavingsAccount() === $this) {
                $transaction->setSavingsAccount(null);
            }
        }
        return $this;
    }

    // Méthodes utilitaires
    public function getTypeLabel(): string
    {
        return match($this->type) {
            self::TYPE_LIVRET_A => 'Livret A',
            self::TYPE_LDDS => 'LDDS (Livret de développement durable)',
            self::TYPE_LEP => 'LEP (Livret d\'épargne populaire)',
            self::TYPE_PEL => 'PEL (Plan épargne logement)',
            self::TYPE_CEL => 'CEL (Compte épargne logement)',
            self::TYPE_LAJ => 'Livret A Jeune',
            self::TYPE_LDD => 'LDD (Livret de développement durable)',
            self::TYPE_LEP_PLUS => 'LEP+',
            self::TYPE_TERM_DEPOSIT => 'Dépôt à terme',
            self::TYPE_SAVINGS_BOND => 'Obligation d\'épargne',
            self::TYPE_ASSURANCE_VIE => 'Assurance vie',
            self::TYPE_PEA => 'PEA (Plan d\'épargne en actions)',
            self::TYPE_PEA_PME => 'PEA-PME',
            default => 'Compte d\'épargne'
        };
    }

    public function getDisplayName(): string
    {
        $bank = $this->bankName ? " - {$this->bankName}" : '';
        return $this->getTypeLabel() . $bank;
    }

    public function getRemainingCapacity(): ?float
    {
        if (!$this->maxAmount || !$this->currentBalance) {
            return null;
        }
        return max(0, $this->maxAmount - $this->currentBalance);
    }

    public function getAnnualInterest(): ?float
    {
        if (!$this->currentBalance || !$this->annualRate) {
            return null;
        }
        return $this->currentBalance * ($this->annualRate / 100);
    }

    public function isRegulated(): bool
    {
        return in_array($this->type, [
            self::TYPE_LIVRET_A,
            self::TYPE_LDDS,
            self::TYPE_LEP,
            self::TYPE_PEL,
            self::TYPE_CEL,
            self::TYPE_LAJ,
            self::TYPE_LDD,
            self::TYPE_LEP_PLUS
        ]);
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
