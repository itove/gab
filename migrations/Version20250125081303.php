<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250125081303 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE stage ALTER grades TYPE TEXT');
        $this->addSql('ALTER TABLE stage ALTER grades DROP NOT NULL');
        $this->addSql('COMMENT ON COLUMN stage.grades IS \'(DC2Type:simple_array)\'');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE stage ALTER grades TYPE VARCHAR(255)');
        $this->addSql('ALTER TABLE stage ALTER grades SET NOT NULL');
        $this->addSql('COMMENT ON COLUMN stage.grades IS NULL');
    }
}
