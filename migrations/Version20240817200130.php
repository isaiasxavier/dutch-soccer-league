<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240817200130 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE coach (id INT NOT NULL, team_id INT NOT NULL, first_name VARCHAR(255) NOT NULL, lastname VARCHAR(255) NOT NULL, name VARCHAR(255) NOT NULL, date DATE NOT NULL, nationality VARCHAR(255) NOT NULL, contract_start VARCHAR(255) NOT NULL, contract_until VARCHAR(255) NOT NULL, UNIQUE INDEX UNIQ_3F596DCC296CD8AE (team_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE competition (id INT NOT NULL, data JSON NOT NULL, name VARCHAR(255) NOT NULL, code VARCHAR(10) NOT NULL, type VARCHAR(50) NOT NULL, emblem VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE game_match (id INT AUTO_INCREMENT NOT NULL, home_team_id INT DEFAULT NULL, away_team_id INT DEFAULT NULL, status VARCHAR(50) DEFAULT NULL, matchday INT DEFAULT NULL, stage VARCHAR(50) DEFAULT NULL, last_updated DATETIME DEFAULT NULL, home_team_name VARCHAR(100) DEFAULT NULL, away_team_name VARCHAR(100) DEFAULT NULL, home_team_score_full_time INT DEFAULT NULL, away_team_score_full_time INT DEFAULT NULL, home_team_score_half_time INT DEFAULT NULL, away_team_score_half_time INT DEFAULT NULL, score_winner VARCHAR(50) DEFAULT NULL, score_duration VARCHAR(50) DEFAULT NULL, referee_id INT DEFAULT NULL, referee_name VARCHAR(255) DEFAULT NULL, INDEX IDX_4868BC8A9C4C13F6 (home_team_id), INDEX IDX_4868BC8A45185D02 (away_team_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE player (id INT NOT NULL, team_id INT NOT NULL, name VARCHAR(255) NOT NULL, position VARCHAR(100) NOT NULL, date DATE NOT NULL, nationality VARCHAR(255) NOT NULL, INDEX IDX_98197A65296CD8AE (team_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE running_competition (id INT NOT NULL, team_id INT NOT NULL, name VARCHAR(255) NOT NULL, code VARCHAR(10) NOT NULL, type VARCHAR(50) NOT NULL, emblem VARCHAR(255) DEFAULT NULL, INDEX IDX_400A43B0296CD8AE (team_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE season (id INT NOT NULL, competition_id INT NOT NULL, start_date DATE NOT NULL, end_date DATE NOT NULL, current_matchday INT NOT NULL, winner VARCHAR(255) DEFAULT NULL, INDEX IDX_F0E45BA97B39D312 (competition_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE season_team_standing (id INT AUTO_INCREMENT NOT NULL, standing_id INT NOT NULL, team_id INT NOT NULL, position INT NOT NULL, played_games INT NOT NULL, form VARCHAR(255) DEFAULT NULL, won INT NOT NULL, draw INT NOT NULL, lost INT NOT NULL, points INT NOT NULL, goals_for INT NOT NULL, goals_against INT NOT NULL, goal_difference INT NOT NULL, INDEX IDX_BADAA9DB346DAB42 (standing_id), INDEX IDX_BADAA9DB296CD8AE (team_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE standing (id INT AUTO_INCREMENT NOT NULL, season_id INT NOT NULL, stage VARCHAR(255) NOT NULL, type VARCHAR(50) NOT NULL, group_name VARCHAR(255) DEFAULT NULL, INDEX IDX_619A8AD84EC001D1 (season_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE team (id INT NOT NULL, name VARCHAR(255) NOT NULL, short_name VARCHAR(100) NOT NULL, tla VARCHAR(10) NOT NULL, crest VARCHAR(255) NOT NULL, address VARCHAR(255) NOT NULL, website VARCHAR(255) NOT NULL, founded VARCHAR(50) NOT NULL, club_colors VARCHAR(255) NOT NULL, venue VARCHAR(255) NOT NULL, last_updated DATETIME NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user (id INT AUTO_INCREMENT NOT NULL, email VARCHAR(180) NOT NULL, roles JSON NOT NULL, password VARCHAR(255) NOT NULL, UNIQUE INDEX UNIQ_IDENTIFIER_EMAIL (email), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE messenger_messages (id BIGINT AUTO_INCREMENT NOT NULL, body LONGTEXT NOT NULL, headers LONGTEXT NOT NULL, queue_name VARCHAR(190) NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', available_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', delivered_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_75EA56E0FB7336F0 (queue_name), INDEX IDX_75EA56E0E3BD61CE (available_at), INDEX IDX_75EA56E016BA31DB (delivered_at), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE coach ADD CONSTRAINT FK_3F596DCC296CD8AE FOREIGN KEY (team_id) REFERENCES team (id)');
        $this->addSql('ALTER TABLE game_match ADD CONSTRAINT FK_4868BC8A9C4C13F6 FOREIGN KEY (home_team_id) REFERENCES team (id)');
        $this->addSql('ALTER TABLE game_match ADD CONSTRAINT FK_4868BC8A45185D02 FOREIGN KEY (away_team_id) REFERENCES team (id)');
        $this->addSql('ALTER TABLE player ADD CONSTRAINT FK_98197A65296CD8AE FOREIGN KEY (team_id) REFERENCES team (id)');
        $this->addSql('ALTER TABLE running_competition ADD CONSTRAINT FK_400A43B0296CD8AE FOREIGN KEY (team_id) REFERENCES team (id)');
        $this->addSql('ALTER TABLE season ADD CONSTRAINT FK_F0E45BA97B39D312 FOREIGN KEY (competition_id) REFERENCES competition (id)');
        $this->addSql('ALTER TABLE season_team_standing ADD CONSTRAINT FK_BADAA9DB346DAB42 FOREIGN KEY (standing_id) REFERENCES standing (id)');
        $this->addSql('ALTER TABLE season_team_standing ADD CONSTRAINT FK_BADAA9DB296CD8AE FOREIGN KEY (team_id) REFERENCES team (id)');
        $this->addSql('ALTER TABLE standing ADD CONSTRAINT FK_619A8AD84EC001D1 FOREIGN KEY (season_id) REFERENCES season (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE coach DROP FOREIGN KEY FK_3F596DCC296CD8AE');
        $this->addSql('ALTER TABLE game_match DROP FOREIGN KEY FK_4868BC8A9C4C13F6');
        $this->addSql('ALTER TABLE game_match DROP FOREIGN KEY FK_4868BC8A45185D02');
        $this->addSql('ALTER TABLE player DROP FOREIGN KEY FK_98197A65296CD8AE');
        $this->addSql('ALTER TABLE running_competition DROP FOREIGN KEY FK_400A43B0296CD8AE');
        $this->addSql('ALTER TABLE season DROP FOREIGN KEY FK_F0E45BA97B39D312');
        $this->addSql('ALTER TABLE season_team_standing DROP FOREIGN KEY FK_BADAA9DB346DAB42');
        $this->addSql('ALTER TABLE season_team_standing DROP FOREIGN KEY FK_BADAA9DB296CD8AE');
        $this->addSql('ALTER TABLE standing DROP FOREIGN KEY FK_619A8AD84EC001D1');
        $this->addSql('DROP TABLE coach');
        $this->addSql('DROP TABLE competition');
        $this->addSql('DROP TABLE game_match');
        $this->addSql('DROP TABLE player');
        $this->addSql('DROP TABLE running_competition');
        $this->addSql('DROP TABLE season');
        $this->addSql('DROP TABLE season_team_standing');
        $this->addSql('DROP TABLE standing');
        $this->addSql('DROP TABLE team');
        $this->addSql('DROP TABLE user');
        $this->addSql('DROP TABLE messenger_messages');
    }
}
