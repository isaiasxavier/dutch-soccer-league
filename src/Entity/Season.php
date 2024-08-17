<?php

namespace App\Entity;

use App\Repository\SeasonRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: SeasonRepository::class)]
class Season
{
    #[ORM\Id]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $start_date = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $end_date = null;

    #[ORM\Column]
    private ?int $current_matchday = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $winner = null;

    #[ORM\ManyToOne(inversedBy: 'season')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Competition $competition = null;

    /**
     * @var Collection<int, Standing>
     */
    #[ORM\OneToMany(targetEntity: Standing::class, mappedBy: 'season')]
    private Collection $standing;

    public function __construct()
    {
        $this->standing = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(int $id): self
    {
        $this->id = $id;

        return $this;
    }

    public function getStartDate(): ?\DateTimeInterface
    {
        return $this->start_date;
    }

    public function setStartDate(\DateTimeInterface $start_date): static
    {
        $this->start_date = $start_date;

        return $this;
    }

    public function getEndDate(): ?\DateTimeInterface
    {
        return $this->end_date;
    }

    public function setEndDate(\DateTimeInterface $end_date): static
    {
        $this->end_date = $end_date;

        return $this;
    }

    public function getCurrentMatchday(): ?int
    {
        return $this->current_matchday;
    }

    public function setCurrentMatchday(int $current_matchday): static
    {
        $this->current_matchday = $current_matchday;

        return $this;
    }

    public function getWinner(): ?string
    {
        return $this->winner;
    }

    public function setWinner(?string $winner): static
    {
        $this->winner = $winner;

        return $this;
    }

    public function getCompetition(): ?Competition
    {
        return $this->competition;
    }

    public function setCompetition(?Competition $competition): static
    {
        $this->competition = $competition;

        return $this;
    }

    /**
     * @return Collection<int, Standing>
     */
    public function getStanding(): Collection
    {
        return $this->standing;
    }

    public function addStanding(Standing $standing): static
    {
        if (!$this->standing->contains($standing)) {
            $this->standing->add($standing);
            $standing->setSeason($this);
        }

        return $this;
    }

    public function removeStanding(Standing $standing): static
    {
        if ($this->standing->removeElement($standing)) {
            // set the owning side to null (unless already changed)
            if ($standing->getSeason() === $this) {
                $standing->setSeason(null);
            }
        }

        return $this;
    }
}
