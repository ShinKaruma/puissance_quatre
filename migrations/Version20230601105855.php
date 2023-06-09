<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230601105855 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE grille (id INT AUTO_INCREMENT NOT NULL, hauteur INT NOT NULL, largeur INT NOT NULL, is_pleine TINYINT(1) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE partie (id INT AUTO_INCREMENT NOT NULL, grille_id INT NOT NULL, player1_id INT DEFAULT NULL, player2_id INT DEFAULT NULL, score_p1 INT NOT NULL, score_p2 INT NOT NULL, UNIQUE INDEX UNIQ_59B1F3D985C2966 (grille_id), INDEX IDX_59B1F3DC0990423 (player1_id), INDEX IDX_59B1F3DD22CABCD (player2_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE pion (id INT AUTO_INCREMENT NOT NULL, partie_id INT DEFAULT NULL, pos_ver INT NOT NULL, pos_hor INT NOT NULL, INDEX IDX_4512B418E075F7A4 (partie_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user (id INT AUTO_INCREMENT NOT NULL, username VARCHAR(180) NOT NULL, roles JSON NOT NULL, password VARCHAR(255) NOT NULL, UNIQUE INDEX UNIQ_8D93D649F85E0677 (username), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE partie ADD CONSTRAINT FK_59B1F3D985C2966 FOREIGN KEY (grille_id) REFERENCES grille (id)');
        $this->addSql('ALTER TABLE partie ADD CONSTRAINT FK_59B1F3DC0990423 FOREIGN KEY (player1_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE partie ADD CONSTRAINT FK_59B1F3DD22CABCD FOREIGN KEY (player2_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE pion ADD CONSTRAINT FK_4512B418E075F7A4 FOREIGN KEY (partie_id) REFERENCES partie (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE partie DROP FOREIGN KEY FK_59B1F3D985C2966');
        $this->addSql('ALTER TABLE partie DROP FOREIGN KEY FK_59B1F3DC0990423');
        $this->addSql('ALTER TABLE partie DROP FOREIGN KEY FK_59B1F3DD22CABCD');
        $this->addSql('ALTER TABLE pion DROP FOREIGN KEY FK_4512B418E075F7A4');
        $this->addSql('DROP TABLE grille');
        $this->addSql('DROP TABLE partie');
        $this->addSql('DROP TABLE pion');
        $this->addSql('DROP TABLE user');
    }
}
