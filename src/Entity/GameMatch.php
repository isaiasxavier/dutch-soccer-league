<?php
    
    namespace App\Entity;
    
    use App\Repository\GameMatchRepository;
    use Doctrine\DBAL\Types\Types;
    use Doctrine\ORM\Mapping as ORM;
    
    #[\AllowDynamicProperties] #[ORM\Entity(repositoryClass: GameMatchRepository::class)]
    class GameMatch
    {
        #[ORM\Id]
        #[ORM\GeneratedValue]
        #[ORM\Column]
        private ?int $id = null;
        
        #[ORM\Column(length: 50, nullable: true)]
        private ?string $status = null;
        
        #[ORM\Column(nullable: true)]
        private ?int $matchday = null;
        
        #[ORM\Column(length: 50, nullable: true)]
        private ?string $stage = null;
        
        #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
        private ?\DateTimeInterface $lastUpdated = null;
        
        #[ORM\Column(nullable: true)]
        private ?int $homeTeamId = null;
        
        #[ORM\Column(nullable: true)]
        private ?int $awayTeamId = null;
        
        #[ORM\Column(nullable: true)]
        private ?int $homeTeamScoreFullTime = null;
        
        #[ORM\Column(nullable: true)]
        private ?int $awayTeamScoreFullTime = null;
        
        #[ORM\Column(nullable: true)]
        private ?int $homeTeamScoreHalfTime = null;
        
        #[ORM\Column(nullable: true)]
        private ?int $awayTeamScoreHalfTime = null;
        
        #[ORM\Column(length: 50, nullable: true)]
        private ?string $scoreWinner = null;
        
        #[ORM\Column(length: 50, nullable: true)]
        private ?string $scoreDuration = null;
        
        #[ORM\Column(nullable: true)]
        private ?int $refereeId = null;
        
        #[ORM\Column(length: 255, nullable: true)]
        private ?string $refereeName = null;
        
        #[ORM\ManyToOne(inversedBy: 'homeGames')]
        #[ORM\JoinColumn(name: 'home_team_id', referencedColumnName: 'id', nullable: false)]
        private ?Team $homeTeam = null;
        
        #[ORM\ManyToOne(inversedBy: 'awayGames')]
        #[ORM\JoinColumn(name: 'away_team_id', referencedColumnName: 'id', nullable: false)]
        private ?Team $awayTeam = null;

        #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
        private ?\DateTimeInterface $dateGame = null;
        
        
        public function getId(): ?int
        {
            return $this->id;
        }
        
        public function setId(?int $id): GameMatch
        {
            $this->id = $id;
            
            return $this;
        }
        
        public function getStatus(): ?string
        {
            return $this->status;
        }
        
        public function setStatus(?string $status): GameMatch
        {
            $this->status = $status;
            
            return $this;
        }
        
        public function getMatchday(): ?int
        {
            return $this->matchday;
        }
        
        public function setMatchday(?int $matchday): GameMatch
        {
            $this->matchday = $matchday;
            
            return $this;
        }
        
        public function getStage(): ?string
        {
            return $this->stage;
        }
        
        public function setStage(?string $stage): GameMatch
        {
            $this->stage = $stage;
            
            return $this;
        }
        
        public function getLastUpdated(): ?\DateTimeInterface
        {
            return $this->lastUpdated;
        }
        
        public function setLastUpdated(?\DateTimeInterface $lastUpdated): GameMatch
        {
            $this->lastUpdated = $lastUpdated;
            
            return $this;
        }
        
        public function getHomeTeamId(): ?int
        {
            return $this->homeTeamId;
        }
        
        public function setHomeTeamId(?int $homeTeamId): GameMatch
        {
            $this->homeTeamId = $homeTeamId;
            
            return $this;
        }
        
        public function getAwayTeamId(): ?int
        {
            return $this->awayTeamId;
        }
        
        public function setAwayTeamId(?int $awayTeamId): GameMatch
        {
            $this->awayTeamId = $awayTeamId;
            
            return $this;
        }
        
        public function getHomeTeamScoreFullTime(): ?int
        {
            return $this->homeTeamScoreFullTime;
        }
        
        public function setHomeTeamScoreFullTime(?int $homeTeamScoreFullTime): GameMatch
        {
            $this->homeTeamScoreFullTime = $homeTeamScoreFullTime;
            
            return $this;
        }
        
        public function getAwayTeamScoreFullTime(): ?int
        {
            return $this->awayTeamScoreFullTime;
        }
        
        public function setAwayTeamScoreFullTime(?int $awayTeamScoreFullTime): GameMatch
        {
            $this->awayTeamScoreFullTime = $awayTeamScoreFullTime;
            
            return $this;
        }
        
        public function getHomeTeamScoreHalfTime(): ?int
        {
            return $this->homeTeamScoreHalfTime;
        }
        
        public function setHomeTeamScoreHalfTime(?int $homeTeamScoreHalfTime): GameMatch
        {
            $this->homeTeamScoreHalfTime = $homeTeamScoreHalfTime;
            
            return $this;
        }
        
        public function getAwayTeamScoreHalfTime(): ?int
        {
            return $this->awayTeamScoreHalfTime;
        }
        
        public function setAwayTeamScoreHalfTime(?int $awayTeamScoreHalfTime): GameMatch
        {
            $this->awayTeamScoreHalfTime = $awayTeamScoreHalfTime;
            
            return $this;
        }
        
        public function getScoreWinner(): ?string
        {
            return $this->scoreWinner;
        }
        
        public function setScoreWinner(?string $scoreWinner): GameMatch
        {
            $this->scoreWinner = $scoreWinner;
            
            return $this;
        }
        
        public function getScoreDuration(): ?string
        {
            return $this->scoreDuration;
        }
        
        public function setScoreDuration(?string $scoreDuration): GameMatch
        {
            $this->scoreDuration = $scoreDuration;
            
            return $this;
        }
        
        public function getRefereeId(): ?int
        {
            return $this->refereeId;
        }
        
        public function setRefereeId(?int $refereeId): GameMatch
        {
            $this->refereeId = $refereeId;
            
            return $this;
        }
        
        public function getRefereeName(): ?string
        {
            return $this->refereeName;
        }
        
        public function setRefereeName(?string $refereeName): GameMatch
        {
            $this->refereeName = $refereeName;
            
            return $this;
        }
        
        public function getHomeTeam(): ?Team
        {
            return $this->homeTeam;
        }
        
        public function setHomeTeam(?Team $homeTeam): GameMatch
        {
            $this->homeTeam = $homeTeam;
            
            return $this;
        }
        
        public function getAwayTeam(): ?Team
        {
            return $this->awayTeam;
        }
        
        public function setAwayTeam(?Team $awayTeam): GameMatch
        {
            $this->awayTeam = $awayTeam;
            
            return $this;
        }

        public function getDateGame(): ?\DateTimeInterface
        {
            return $this->dateGame;
        }

        public function setDateGame(?\DateTimeInterface $dateGame): static
        {
            $this->dateGame = $dateGame;

            return $this;
        }

        
        
    }