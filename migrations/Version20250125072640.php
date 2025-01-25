<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250125072640 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE school (id SERIAL NOT NULL, name VARCHAR(255) NOT NULL, province VARCHAR(255) NOT NULL, city VARCHAR(255) NOT NULL, area VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE school_stage (school_id INT NOT NULL, stage_id INT NOT NULL, PRIMARY KEY(school_id, stage_id))');
        $this->addSql('CREATE INDEX IDX_1C862573C32A47EE ON school_stage (school_id)');
        $this->addSql('CREATE INDEX IDX_1C8625732298D193 ON school_stage (stage_id)');
        $this->addSql('ALTER TABLE school_stage ADD CONSTRAINT FK_1C862573C32A47EE FOREIGN KEY (school_id) REFERENCES school (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE school_stage ADD CONSTRAINT FK_1C8625732298D193 FOREIGN KEY (stage_id) REFERENCES stage (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE school_stage DROP CONSTRAINT FK_1C862573C32A47EE');
        $this->addSql('ALTER TABLE school_stage DROP CONSTRAINT FK_1C8625732298D193');
        $this->addSql('DROP TABLE school');
        $this->addSql('DROP TABLE school_stage');
    }
}
