<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260105190832 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE grade_cost (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, grade_level VARCHAR(50) NOT NULL, enrollment_fee NUMERIC(10, 2) NOT NULL, monthly_fee NUMERIC(10, 2) NOT NULL)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_B644D9EC87F3BE3A ON grade_cost (grade_level)');
        $this->addSql('CREATE TABLE log (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, "action" VARCHAR(50) NOT NULL, username VARCHAR(100) NOT NULL, details CLOB DEFAULT NULL, ip VARCHAR(45) DEFAULT NULL, created_at DATETIME NOT NULL)');
        $this->addSql('CREATE TABLE request_entity (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, type VARCHAR(50) NOT NULL, status VARCHAR(20) NOT NULL, reason CLOB NOT NULL, document_reference VARCHAR(255) DEFAULT NULL, created_at DATETIME NOT NULL, processed_at DATETIME DEFAULT NULL, requested_by_id INTEGER NOT NULL, student_id INTEGER DEFAULT NULL, CONSTRAINT FK_457187934DA1E751 FOREIGN KEY (requested_by_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_45718793CB944F1A FOREIGN KEY (student_id) REFERENCES student (id) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('CREATE INDEX IDX_457187934DA1E751 ON request_entity (requested_by_id)');
        $this->addSql('CREATE INDEX IDX_45718793CB944F1A ON request_entity (student_id)');
        $this->addSql('CREATE TABLE scholarship (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, name VARCHAR(100) NOT NULL, type VARCHAR(20) NOT NULL, value NUMERIC(10, 2) NOT NULL, description CLOB DEFAULT NULL, is_active BOOLEAN NOT NULL, created_at DATETIME NOT NULL, school_cycle_id INTEGER NOT NULL, CONSTRAINT FK_F3FD63FFBAA2526 FOREIGN KEY (school_cycle_id) REFERENCES school_cycle (id) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('CREATE INDEX IDX_F3FD63FFBAA2526 ON scholarship (school_cycle_id)');
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
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE grade_cost');
        $this->addSql('DROP TABLE log');
        $this->addSql('DROP TABLE request_entity');
        $this->addSql('DROP TABLE scholarship');
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
    }
}
