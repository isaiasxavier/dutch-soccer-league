<?php

namespace App\Entity;

use App\Repository\StandingRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: StandingRepository::class)]
class Standing
{
    #[ORM\Id]
    #[ORM\GeneratedValue()]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $stage = null;

    #[ORM\Column(length: 50)]
    private ?string $type = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $group_name = null;

    #[ORM\ManyToOne(inversedBy: 'standing')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Season $season = null;

    /**
     * @var Collection<int, SeasonTeamStanding>
     */
    #[ORM\OneToMany(targetEntity: SeasonTeamStanding::class, mappedBy: 'standing')]
    private Collection $season_team_standing;

    public function __construct()
    {
        $this->season_team_standing = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }
    
    public function getStage(): ?string
    {
        return $this->stage;
    }

    public function setStage(string $stage): static
    {
        $this->stage = $stage;

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

    public function getGroupName(): ?string
    {
        return $this->group_name;
    }

    public function setGroupName(?string $group_name): static
    {
        $this->group_name = $group_name;

        return $this;
    }

    public function getSeason(): ?Season
    {
        return $this->season;
    }

    public function setSeason(?Season $season): static
    {
        $this->season = $season;

        return $this;
    }

    /**
     * @return Collection<int, SeasonTeamStanding>
     */
    public function getSeasonTeamStanding(): Collection
    {
        return $this->season_team_standing;
    }

    public function addSeasonTeamStanding(SeasonTeamStanding $seasonTeamStanding): static
    {
        if (!$this->season_team_standing->contains($seasonTeamStanding)) {
            $this->season_team_standing->add($seasonTeamStanding);
            $seasonTeamStanding->setStanding($this);
        }

        return $this;
    }

    public function removeSeasonTeamStanding(SeasonTeamStanding $seasonTeamStanding): static
    {
        if ($this->season_team_standing->removeElement($seasonTeamStanding)) {
            // set the owning side to null (unless already changed)
            if ($seasonTeamStanding->getStanding() === $this) {
                $seasonTeamStanding->setStanding(null);
            }
        }

        return $this;
    }
}
