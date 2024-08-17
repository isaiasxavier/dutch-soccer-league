<?php

namespace App\Entity;

use App\Repository\TeamRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: TeamRepository::class)]
class Team
{
    #[ORM\Id]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column(length: 100)]
    private ?string $shortName = null;

    #[ORM\Column(length: 10)]
    private ?string $tla = null;

    #[ORM\Column(length: 255)]
    private ?string $crest = null;

    #[ORM\Column(length: 255)]
    private ?string $address = null;

    #[ORM\Column(length: 255)]
    private ?string $website = null;

    #[ORM\Column(length: 50)]
    private ?string $founded = null;

    #[ORM\Column(length: 255)]
    private ?string $clubColors = null;

    #[ORM\Column(length: 255)]
    private ?string $venue = null;

    #[ORM\Column(type: 'datetime')]
    private ?\DateTimeInterface $lastUpdated = null;

    /**
     * @var Collection<int, SeasonTeamStanding>
     */
    #[ORM\OneToMany(targetEntity: SeasonTeamStanding::class, mappedBy: 'team')]
    private Collection $season_team_standing;

    /**
     * @var Collection<int, GameMatch>
     */
    #[ORM\OneToMany(targetEntity: GameMatch::class, mappedBy: 'team')]
    private Collection $gameMatch;

    public function __construct()
    {
        $this->season_team_standing = new ArrayCollection();
        $this->gameMatch = new ArrayCollection();
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

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getShortName(): ?string
    {
        return $this->shortName;
    }

    public function setShortName(string $shortName): static
    {
        $this->shortName = $shortName;

        return $this;
    }

    public function getTla(): ?string
    {
        return $this->tla;
    }

    public function setTla(string $tla): static
    {
        $this->tla = $tla;

        return $this;
    }

    public function getCrest(): ?string
    {
        return $this->crest;
    }

    public function setCrest(string $crest): static
    {
        $this->crest = $crest;

        return $this;
    }

    public function getAddress(): ?string
    {
        return $this->address;
    }

    public function setAddress(string $address): static
    {
        $this->address = $address;

        return $this;
    }

    public function getWebsite(): ?string
    {
        return $this->website;
    }

    public function setWebsite(string $website): static
    {
        $this->website = $website;

        return $this;
    }

    public function getFounded(): ?string
    {
        return $this->founded;
    }

    public function setFounded(string $founded): static
    {
        $this->founded = $founded;

        return $this;
    }

    public function getClubColors(): ?string
    {
        return $this->clubColors;
    }

    public function setClubColors(string $clubColors): static
    {
        $this->clubColors = $clubColors;

        return $this;
    }

    public function getVenue(): ?string
    {
        return $this->venue;
    }

    public function setVenue(string $venue): static
    {
        $this->venue = $venue;

        return $this;
    }

    public function getLastUpdated(): ?\DateTimeInterface
    {
        return $this->lastUpdated;
    }

    public function setLastUpdated(?\DateTimeInterface $lastUpdated): static
    {
        $this->lastUpdated = $lastUpdated;

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
            $seasonTeamStanding->setTeam($this);
        }

        return $this;
    }

    public function removeSeasonTeamStanding(SeasonTeamStanding $seasonTeamStanding): static
    {
        if ($this->season_team_standing->removeElement($seasonTeamStanding)) {
            // set the owning side to null (unless already changed)
            if ($seasonTeamStanding->getTeam() === $this) {
                $seasonTeamStanding->setTeam(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, GameMatchBKUP>
     */
    public function getGameMatch(): Collection
    {
        return $this->gameMatch;
    }

    /*public function addGameMatch(GameMatch $gameMatch): static
    {
        if (!$this->gameMatch->contains($gameMatch)) {
            $this->gameMatch->add($gameMatch);
            $gameMatch->setTeam($this);
        }

        return $this;
    }

    public function removeGameMatch(GameMatch $gameMatch): static
    {
        if ($this->gameMatch->removeElement($gameMatch)) {
            // set the owning side to null (unless already changed)
            if ($gameMatch->getTeam() === $this) {
                $gameMatch->setTeam(null);
            }
        }

        return $this;
    }*/
}
