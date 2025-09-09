<?php

namespace App\Entity;

use App\Repository\UserSubscriptionRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: UserSubscriptionRepository::class)]
#[ORM\Table(name: 'user_subscriptions')]
class UserSubscription
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\OneToOne(targetEntity: User::class, inversedBy: 'subscription')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $user = null;

    #[ORM\Column(length: 50)]
    private ?string $plan = null;

    #[ORM\Column(length: 100)]
    private ?string $stripeSubscriptionId = null;

    #[ORM\Column(length: 100)]
    private ?string $stripeCustomerId = null;

    #[ORM\Column(length: 100)]
    private ?string $stripePriceId = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $currentPeriodStart = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $currentPeriodEnd = null;

    #[ORM\Column(length: 20)]
    private ?string $status = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $updatedAt = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $canceledAt = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $trialEndsAt = null;

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

    public function getPlan(): ?string
    {
        return $this->plan;
    }

    public function setPlan(string $plan): static
    {
        $this->plan = $plan;
        return $this;
    }

    public function getStripeSubscriptionId(): ?string
    {
        return $this->stripeSubscriptionId;
    }

    public function setStripeSubscriptionId(string $stripeSubscriptionId): static
    {
        $this->stripeSubscriptionId = $stripeSubscriptionId;
        return $this;
    }

    public function getStripeCustomerId(): ?string
    {
        return $this->stripeCustomerId;
    }

    public function setStripeCustomerId(string $stripeCustomerId): static
    {
        $this->stripeCustomerId = $stripeCustomerId;
        return $this;
    }

    public function getStripePriceId(): ?string
    {
        return $this->stripePriceId;
    }

    public function setStripePriceId(string $stripePriceId): static
    {
        $this->stripePriceId = $stripePriceId;
        return $this;
    }

    public function getCurrentPeriodStart(): ?\DateTimeImmutable
    {
        return $this->currentPeriodStart;
    }

    public function setCurrentPeriodStart(\DateTimeImmutable $currentPeriodStart): static
    {
        $this->currentPeriodStart = $currentPeriodStart;
        return $this;
    }

    public function getCurrentPeriodEnd(): ?\DateTimeImmutable
    {
        return $this->currentPeriodEnd;
    }

    public function setCurrentPeriodEnd(\DateTimeImmutable $currentPeriodEnd): static
    {
        $this->currentPeriodEnd = $currentPeriodEnd;
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

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): static
    {
        $this->createdAt = $createdAt;
        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeImmutable
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(\DateTimeImmutable $updatedAt): static
    {
        $this->updatedAt = $updatedAt;
        return $this;
    }

    public function getCanceledAt(): ?\DateTimeImmutable
    {
        return $this->canceledAt;
    }

    public function setCanceledAt(?\DateTimeImmutable $canceledAt): static
    {
        $this->canceledAt = $canceledAt;
        return $this;
    }

    public function getTrialEndsAt(): ?\DateTimeImmutable
    {
        return $this->trialEndsAt;
    }

    public function setTrialEndsAt(?\DateTimeImmutable $trialEndsAt): static
    {
        $this->trialEndsAt = $trialEndsAt;
        return $this;
    }

    public function isActive(): bool
    {
        return in_array($this->status, ['active', 'trialing']);
    }

    public function isTrialing(): bool
    {
        return $this->status === 'trialing' && 
               $this->trialEndsAt && 
               $this->trialEndsAt > new \DateTimeImmutable();
    }

    public function isExpired(): bool
    {
        return $this->currentPeriodEnd && $this->currentPeriodEnd < new \DateTimeImmutable();
    }

    public function getPlanLimits(): array
    {
        return match($this->plan) {
            'free' => [
                'max_cryptos' => 3,
                'max_transactions' => 10,
                'pdf_reports' => false,
                'api_calls_per_day' => 100,
                'portfolio_tracking' => true,
                'basic_analytics' => true,
            ],
            'pro' => [
                'max_cryptos' => 50,
                'max_transactions' => 1000,
                'pdf_reports' => true,
                'api_calls_per_day' => 1000,
                'portfolio_tracking' => true,
                'advanced_analytics' => true,
                'export_data' => true,
            ],
            'enterprise' => [
                'max_cryptos' => -1, // unlimited
                'max_transactions' => -1, // unlimited
                'pdf_reports' => true,
                'api_calls_per_day' => 10000,
                'portfolio_tracking' => true,
                'advanced_analytics' => true,
                'export_data' => true,
                'priority_support' => true,
                'custom_integrations' => true,
            ],
            default => [
                'max_cryptos' => 3,
                'max_transactions' => 10,
                'pdf_reports' => false,
                'api_calls_per_day' => 100,
                'portfolio_tracking' => true,
                'basic_analytics' => true,
            ]
        };
    }
}
