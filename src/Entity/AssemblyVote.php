<?php

namespace App\Entity;

use App\Repository\AssemblyVoteRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: AssemblyVoteRepository::class)]
#[ORM\Table(name: 'assembly_votes')]
class AssemblyVote
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $user = null;

    #[ORM\ManyToOne(targetEntity: Assembly::class, inversedBy: 'votes')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Assembly $assembly = null;

    #[ORM\Column(length: 255)]
    private ?string $resolution = null;

    #[ORM\Column(length: 20)]
    private ?string $vote = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $votedAt = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $comment = null;

    public function __construct()
    {
        $this->votedAt = new \DateTime();
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

    public function getAssembly(): ?Assembly
    {
        return $this->assembly;
    }

    public function setAssembly(?Assembly $assembly): static
    {
        $this->assembly = $assembly;
        return $this;
    }

    public function getResolution(): ?string
    {
        return $this->resolution;
    }

    public function setResolution(string $resolution): static
    {
        $this->resolution = $resolution;
        return $this;
    }

    public function getVote(): ?string
    {
        return $this->vote;
    }

    public function setVote(string $vote): static
    {
        $this->vote = $vote;
        return $this;
    }

    public function getVotedAt(): ?\DateTimeInterface
    {
        return $this->votedAt;
    }

    public function setVotedAt(\DateTimeInterface $votedAt): static
    {
        $this->votedAt = $votedAt;
        return $this;
    }

    public function getComment(): ?string
    {
        return $this->comment;
    }

    public function setComment(?string $comment): static
    {
        $this->comment = $comment;
        return $this;
    }

    public function isFor(): bool
    {
        return $this->vote === 'yes';
    }

    public function isAgainst(): bool
    {
        return $this->vote === 'no';
    }

    public function isAbstention(): bool
    {
        return $this->vote === 'abstain';
    }
}
