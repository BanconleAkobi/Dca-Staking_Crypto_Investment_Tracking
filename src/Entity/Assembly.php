<?php

namespace App\Entity;

use App\Repository\AssemblyRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: AssemblyRepository::class)]
#[ORM\Table(name: 'assemblies')]
class Assembly
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $company = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $date = null;

    #[ORM\Column(length: 10)]
    private ?string $type = null;

    #[ORM\Column(length: 20)]
    private ?string $status = null;

    #[ORM\Column(type: Types::BOOLEAN)]
    private bool $votingOpen = false;

    #[ORM\Column(type: Types::JSON)]
    private array $resolutions = [];

    #[ORM\Column(type: Types::JSON)]
    private array $documents = [];

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $description = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $createdAt = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $updatedAt = null;

    #[ORM\OneToMany(targetEntity: AssemblyVote::class, mappedBy: 'assembly', cascade: ['persist', 'remove'])]
    private Collection $votes;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $videoConferenceUrl = null;

    #[ORM\Column(type: Types::BOOLEAN)]
    private bool $videoConferenceEnabled = false;

    public function __construct()
    {
        $this->createdAt = new \DateTime();
        $this->votes = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCompany(): ?string
    {
        return $this->company;
    }

    public function setCompany(string $company): static
    {
        $this->company = $company;
        return $this;
    }

    public function getDate(): ?\DateTimeInterface
    {
        return $this->date;
    }

    public function setDate(\DateTimeInterface $date): static
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

    public function isVotingOpen(): bool
    {
        return $this->votingOpen;
    }

    public function setVotingOpen(bool $votingOpen): static
    {
        $this->votingOpen = $votingOpen;
        return $this;
    }

    public function getResolutions(): array
    {
        return $this->resolutions;
    }

    public function setResolutions(array $resolutions): static
    {
        $this->resolutions = $resolutions;
        return $this;
    }

    public function addResolution(string $resolution): static
    {
        if (!in_array($resolution, $this->resolutions)) {
            $this->resolutions[] = $resolution;
        }
        return $this;
    }

    public function removeResolution(string $resolution): static
    {
        $key = array_search($resolution, $this->resolutions);
        if ($key !== false) {
            unset($this->resolutions[$key]);
            $this->resolutions = array_values($this->resolutions);
        }
        return $this;
    }

    public function getDocuments(): array
    {
        return $this->documents;
    }

    public function setDocuments(array $documents): static
    {
        $this->documents = $documents;
        return $this;
    }

    public function addDocument(string $document): static
    {
        if (!in_array($document, $this->documents)) {
            $this->documents[] = $document;
        }
        return $this;
    }

    public function removeDocument(string $document): static
    {
        $key = array_search($document, $this->documents);
        if ($key !== false) {
            unset($this->documents[$key]);
            $this->documents = array_values($this->documents);
        }
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

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeInterface $createdAt): static
    {
        $this->createdAt = $createdAt;
        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeInterface
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(?\DateTimeInterface $updatedAt): static
    {
        $this->updatedAt = $updatedAt;
        return $this;
    }

    /**
     * @return Collection<int, AssemblyVote>
     */
    public function getVotes(): Collection
    {
        return $this->votes;
    }

    public function addVote(AssemblyVote $vote): static
    {
        if (!$this->votes->contains($vote)) {
            $this->votes->add($vote);
            $vote->setAssembly($this);
        }
        return $this;
    }

    public function removeVote(AssemblyVote $vote): static
    {
        if ($this->votes->removeElement($vote)) {
            if ($vote->getAssembly() === $this) {
                $vote->setAssembly(null);
            }
        }
        return $this;
    }

    public function getVideoConferenceUrl(): ?string
    {
        return $this->videoConferenceUrl;
    }

    public function setVideoConferenceUrl(?string $videoConferenceUrl): static
    {
        $this->videoConferenceUrl = $videoConferenceUrl;
        return $this;
    }

    public function isVideoConferenceEnabled(): bool
    {
        return $this->videoConferenceEnabled;
    }

    public function setVideoConferenceEnabled(bool $videoConferenceEnabled): static
    {
        $this->videoConferenceEnabled = $videoConferenceEnabled;
        return $this;
    }

    public function isUpcoming(): bool
    {
        return $this->date > new \DateTime();
    }

    public function isPast(): bool
    {
        return $this->date < new \DateTime();
    }
}
