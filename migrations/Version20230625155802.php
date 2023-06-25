<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230625155802 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE partie ADD player_en_cours_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE partie ADD CONSTRAINT FK_59B1F3D6B1B7FBE FOREIGN KEY (player_en_cours_id) REFERENCES user (id)');
        $this->addSql('CREATE INDEX IDX_59B1F3D6B1B7FBE ON partie (player_en_cours_id)');
        $this->addSql('ALTER TABLE user CHANGE score_total score_total INT NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE partie DROP FOREIGN KEY FK_59B1F3D6B1B7FBE');
        $this->addSql('DROP INDEX IDX_59B1F3D6B1B7FBE ON partie');
        $this->addSql('ALTER TABLE partie DROP player_en_cours_id');
        $this->addSql('ALTER TABLE user CHANGE score_total score_total INT DEFAULT 0');
    }
}
