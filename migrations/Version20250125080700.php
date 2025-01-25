<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250125080700 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE insured (id SERIAL NOT NULL, school_id INT DEFAULT NULL, name VARCHAR(255) NOT NULL, idnum VARCHAR(255) NOT NULL, grade VARCHAR(255) NOT NULL, class VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_560FF44EC32A47EE ON insured (school_id)');
        $this->addSql('ALTER TABLE insured ADD CONSTRAINT FK_560FF44EC32A47EE FOREIGN KEY (school_id) REFERENCES school (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE insured DROP CONSTRAINT FK_560FF44EC32A47EE');
        $this->addSql('DROP TABLE insured');
    }
}
