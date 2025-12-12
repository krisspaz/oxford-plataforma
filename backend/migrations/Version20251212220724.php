<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20251212220724 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE student (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, first_name VARCHAR(255) NOT NULL, last_name VARCHAR(255) NOT NULL, carnet VARCHAR(20) NOT NULL, birth_date DATE NOT NULL, email VARCHAR(255) DEFAULT NULL, address CLOB DEFAULT NULL, is_active BOOLEAN NOT NULL, academic_risk_score DOUBLE PRECISION DEFAULT NULL)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_B723AF33576D2650 ON student (carnet)');
        $this->addSql('CREATE TABLE "user" (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, email VARCHAR(180) NOT NULL, roles CLOB NOT NULL, password VARCHAR(255) NOT NULL)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_8D93D649E7927C74 ON "user" (email)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE student');
        $this->addSql('DROP TABLE "user"');
    }
}
