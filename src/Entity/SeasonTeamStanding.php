<?php

namespace App\Entity;

use App\Repository\SeasonTeamStandingRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: SeasonTeamStandingRepository::class)]
class SeasonTeamStanding
{
    #[ORM\Id]
    #[ORM\GeneratedValue()]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?int $position = null;

    #[ORM\Column]
    private ?int $played_games = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $form = null;

    #[ORM\Column]
    private ?int $won = null;

    #[ORM\Column]
    private ?int $draw = null;

    #[ORM\Column]
    private ?int $lost = null;

    #[ORM\Column]
    private ?int $points = null;

    #[ORM\Column]
    private ?int $goals_for = null;

    #[ORM\Column]
    private ?int $goals_against = null;

    #[ORM\Column]
    private ?int $goal_difference = null;

    #[ORM\ManyToOne(inversedBy: 'season_team_standing')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Standing $standing = null;

    #[ORM\ManyToOne(inversedBy: 'season_team_standing')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Team $team = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPosition(): ?int
    {
        return $this->position;
    }

    public function setPosition(int $position): static
    {
        $this->position = $position;

        return $this;
    }

    public function getPlayedGames(): ?int
    {
        return $this->played_games;
    }

    public function setPlayedGames(int $played_games): static
    {
        $this->played_games = $played_games;

        return $this;
    }

    public function getForm(): ?string
    {
        return $this->form;
    }

    public function setForm(?string $form): static
    {
        $this->form = $form;

        return $this;
    }

    public function getWon(): ?int
    {
        return $this->won;
    }

    public function setWon(int $won): static
    {
        $this->won = $won;

        return $this;
    }

    public function getDraw(): ?int
    {
        return $this->draw;
    }

    public function setDraw(int $draw): static
    {
        $this->draw = $draw;

        return $this;
    }

    public function getLost(): ?int
    {
        return $this->lost;
    }

    public function setLost(int $lost): static
    {
        $this->lost = $lost;

        return $this;
    }

    public function getPoints(): ?int
    {
        return $this->points;
    }

    public function setPoints(int $points): static
    {
        $this->points = $points;

        return $this;
    }

    public function getGoalsFor(): ?int
    {
        return $this->goals_for;
    }

    public function setGoalsFor(int $goals_for): static
    {
        $this->goals_for = $goals_for;

        return $this;
    }

    public function getGoalsAgainst(): ?int
    {
        return $this->goals_against;
    }

    public function setGoalsAgainst(int $goals_against): static
    {
        $this->goals_against = $goals_against;

        return $this;
    }

    public function getGoalDifference(): ?int
    {
        return $this->goal_difference;
    }

    public function setGoalDifference(int $goal_difference): static
    {
        $this->goal_difference = $goal_difference;

        return $this;
    }

    public function getStanding(): ?Standing
    {
        return $this->standing;
    }

    public function setStanding(?Standing $standing): static
    {
        $this->standing = $standing;

        return $this;
    }

    public function getTeam(): ?Team
    {
        return $this->team;
    }

    public function setTeam(?Team $team): static
    {
        $this->team = $team;

        return $this;
    }
}
