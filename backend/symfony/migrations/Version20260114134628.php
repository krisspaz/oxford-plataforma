<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260114134628 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TEMPORARY TABLE __temp__guardian AS SELECT occupation, workplace, work_phone, nit, relationship, id FROM guardian');
        $this->addSql('DROP TABLE guardian');
        $this->addSql('CREATE TABLE guardian (occupation VARCHAR(100) DEFAULT NULL, workplace VARCHAR(100) DEFAULT NULL, work_phone VARCHAR(20) DEFAULT NULL, nit VARCHAR(50) DEFAULT NULL, relationship VARCHAR(50) NOT NULL, id INTEGER NOT NULL, PRIMARY KEY (id), CONSTRAINT FK_64486055BF396750 FOREIGN KEY (id) REFERENCES person (id) ON UPDATE NO ACTION ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('INSERT INTO guardian (occupation, workplace, work_phone, nit, relationship, id) SELECT occupation, workplace, work_phone, nit, relationship, id FROM __temp__guardian');
        $this->addSql('DROP TABLE __temp__guardian');
        $this->addSql('CREATE TEMPORARY TABLE __temp__staff AS SELECT employee_code, position, department, hire_date, contract_type, id FROM staff');
        $this->addSql('DROP TABLE staff');
        $this->addSql('CREATE TABLE staff (employee_code VARCHAR(50) DEFAULT NULL, position VARCHAR(100) NOT NULL, department VARCHAR(100) DEFAULT NULL, hire_date DATE DEFAULT NULL, contract_type VARCHAR(50) NOT NULL, id INTEGER NOT NULL, PRIMARY KEY (id), CONSTRAINT FK_426EF392BF396750 FOREIGN KEY (id) REFERENCES person (id) ON UPDATE NO ACTION ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('INSERT INTO staff (employee_code, position, department, hire_date, contract_type, id) SELECT employee_code, position, department, hire_date, contract_type, id FROM __temp__staff');
        $this->addSql('DROP TABLE __temp__staff');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_426EF392C2CC7ADF ON staff (employee_code)');
        $this->addSql('CREATE TEMPORARY TABLE __temp__teacher AS SELECT employee_code, specialization, hire_date, contract_type, id FROM teacher');
        $this->addSql('DROP TABLE teacher');
        $this->addSql('CREATE TABLE teacher (employee_code VARCHAR(50) DEFAULT NULL, specialization VARCHAR(100) DEFAULT NULL, hire_date DATE DEFAULT NULL, contract_type VARCHAR(50) NOT NULL, id INTEGER NOT NULL, PRIMARY KEY (id), CONSTRAINT FK_B0F6A6D5BF396750 FOREIGN KEY (id) REFERENCES person (id) ON UPDATE NO ACTION ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('INSERT INTO teacher (employee_code, specialization, hire_date, contract_type, id) SELECT employee_code, specialization, hire_date, contract_type, id FROM __temp__teacher');
        $this->addSql('DROP TABLE __temp__teacher');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_B0F6A6D5C2CC7ADF ON teacher (employee_code)');
        $this->addSql('CREATE TEMPORARY TABLE __temp__user AS SELECT id, name, email, roles, password, is_active, two_factor_auth_secret, two_factor_auth_enabled, two_factor_auth_backup_codes, two_factor_auth_enabled_at FROM user');
        $this->addSql('DROP TABLE user');
        $this->addSql('CREATE TABLE user (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, name VARCHAR(100) DEFAULT NULL, email VARCHAR(180) NOT NULL, roles CLOB NOT NULL, password VARCHAR(255) NOT NULL, is_active BOOLEAN DEFAULT 1 NOT NULL, two_factor_auth_secret VARCHAR(64) DEFAULT NULL, two_factor_auth_enabled BOOLEAN NOT NULL, two_factor_auth_backup_codes CLOB DEFAULT NULL, two_factor_auth_enabled_at DATETIME DEFAULT NULL)');
        $this->addSql('INSERT INTO user (id, name, email, roles, password, is_active, two_factor_auth_secret, two_factor_auth_enabled, two_factor_auth_backup_codes, two_factor_auth_enabled_at) SELECT id, name, email, roles, password, is_active, two_factor_auth_secret, two_factor_auth_enabled, two_factor_auth_backup_codes, two_factor_auth_enabled_at FROM __temp__user');
        $this->addSql('DROP TABLE __temp__user');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_8D93D649E7927C74 ON user (email)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TEMPORARY TABLE __temp__guardian AS SELECT occupation, workplace, work_phone, nit, relationship, id FROM guardian');
        $this->addSql('DROP TABLE guardian');
        $this->addSql('CREATE TABLE guardian (occupation VARCHAR(100) DEFAULT NULL, workplace VARCHAR(100) DEFAULT NULL, work_phone VARCHAR(20) DEFAULT NULL, nit VARCHAR(50) DEFAULT NULL, relationship VARCHAR(50) NOT NULL, id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, CONSTRAINT FK_64486055BF396750 FOREIGN KEY (id) REFERENCES person (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('INSERT INTO guardian (occupation, workplace, work_phone, nit, relationship, id) SELECT occupation, workplace, work_phone, nit, relationship, id FROM __temp__guardian');
        $this->addSql('DROP TABLE __temp__guardian');
        $this->addSql('CREATE TEMPORARY TABLE __temp__staff AS SELECT employee_code, position, department, hire_date, contract_type, id FROM staff');
        $this->addSql('DROP TABLE staff');
        $this->addSql('CREATE TABLE staff (employee_code VARCHAR(50) DEFAULT NULL, position VARCHAR(100) NOT NULL, department VARCHAR(100) DEFAULT NULL, hire_date DATE DEFAULT NULL, contract_type VARCHAR(50) NOT NULL, id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, CONSTRAINT FK_426EF392BF396750 FOREIGN KEY (id) REFERENCES person (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('INSERT INTO staff (employee_code, position, department, hire_date, contract_type, id) SELECT employee_code, position, department, hire_date, contract_type, id FROM __temp__staff');
        $this->addSql('DROP TABLE __temp__staff');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_426EF392C2CC7ADF ON staff (employee_code)');
        $this->addSql('CREATE TEMPORARY TABLE __temp__teacher AS SELECT employee_code, specialization, hire_date, contract_type, id FROM teacher');
        $this->addSql('DROP TABLE teacher');
        $this->addSql('CREATE TABLE teacher (employee_code VARCHAR(50) DEFAULT NULL, specialization VARCHAR(100) DEFAULT NULL, hire_date DATE DEFAULT NULL, contract_type VARCHAR(50) NOT NULL, id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, CONSTRAINT FK_B0F6A6D5BF396750 FOREIGN KEY (id) REFERENCES person (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('INSERT INTO teacher (employee_code, specialization, hire_date, contract_type, id) SELECT employee_code, specialization, hire_date, contract_type, id FROM __temp__teacher');
        $this->addSql('DROP TABLE __temp__teacher');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_B0F6A6D5C2CC7ADF ON teacher (employee_code)');
        $this->addSql('CREATE TEMPORARY TABLE __temp__user AS SELECT id, name, email, roles, password, is_active, two_factor_auth_secret, two_factor_auth_enabled, two_factor_auth_backup_codes, two_factor_auth_enabled_at FROM "user"');
        $this->addSql('DROP TABLE "user"');
        $this->addSql('CREATE TABLE "user" (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, name VARCHAR(100) DEFAULT NULL, email VARCHAR(180) NOT NULL, roles CLOB NOT NULL, password VARCHAR(255) NOT NULL, is_active BOOLEAN DEFAULT 1 NOT NULL, two_factor_auth_secret VARCHAR(64) DEFAULT NULL, two_factor_auth_enabled BOOLEAN DEFAULT 0 NOT NULL, two_factor_auth_backup_codes CLOB DEFAULT NULL, two_factor_auth_enabled_at DATETIME DEFAULT NULL)');
        $this->addSql('INSERT INTO "user" (id, name, email, roles, password, is_active, two_factor_auth_secret, two_factor_auth_enabled, two_factor_auth_backup_codes, two_factor_auth_enabled_at) SELECT id, name, email, roles, password, is_active, two_factor_auth_secret, two_factor_auth_enabled, two_factor_auth_backup_codes, two_factor_auth_enabled_at FROM __temp__user');
        $this->addSql('DROP TABLE __temp__user');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_8D93D649E7927C74 ON "user" (email)');
    }
}
