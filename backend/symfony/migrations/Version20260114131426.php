<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260114131426 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE classroom (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, name VARCHAR(100) NOT NULL, capacity INTEGER NOT NULL, features CLOB NOT NULL, location VARCHAR(255) DEFAULT NULL)');
        $this->addSql('DROP TABLE attendance');
        $this->addSql('DROP TABLE schedule');
        $this->addSql('ALTER TABLE audit_log ADD COLUMN user_email VARCHAR(180) DEFAULT NULL');
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
        $this->addSql('ALTER TABLE user ADD COLUMN two_factor_auth_secret VARCHAR(64) DEFAULT NULL');
        $this->addSql('ALTER TABLE user ADD COLUMN two_factor_auth_enabled BOOLEAN DEFAULT 0 NOT NULL');
        $this->addSql('ALTER TABLE user ADD COLUMN two_factor_auth_backup_codes CLOB DEFAULT NULL');
        $this->addSql('ALTER TABLE user ADD COLUMN two_factor_auth_enabled_at DATETIME DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE attendance (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, date DATE NOT NULL, status VARCHAR(20) NOT NULL COLLATE "BINARY", notes CLOB DEFAULT NULL COLLATE "BINARY", created_at DATETIME NOT NULL, student_id INTEGER NOT NULL, schedule_id INTEGER DEFAULT NULL, subject_id INTEGER DEFAULT NULL, teacher_id INTEGER DEFAULT NULL, bimester_id INTEGER DEFAULT NULL, created_by_id INTEGER DEFAULT NULL, CONSTRAINT FK_6DE30D91CB944F1A FOREIGN KEY (student_id) REFERENCES student (id) ON UPDATE NO ACTION ON DELETE NO ACTION NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_6DE30D91A40BC2D5 FOREIGN KEY (schedule_id) REFERENCES schedule (id) ON UPDATE NO ACTION ON DELETE NO ACTION NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_6DE30D9123EDC87 FOREIGN KEY (subject_id) REFERENCES subject (id) ON UPDATE NO ACTION ON DELETE NO ACTION NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_6DE30D9141807E1D FOREIGN KEY (teacher_id) REFERENCES teacher (id) ON UPDATE NO ACTION ON DELETE NO ACTION NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_6DE30D913F3D70EA FOREIGN KEY (bimester_id) REFERENCES bimester (id) ON UPDATE NO ACTION ON DELETE NO ACTION NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_6DE30D91B03A8386 FOREIGN KEY (created_by_id) REFERENCES user (id) ON UPDATE NO ACTION ON DELETE NO ACTION NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('CREATE UNIQUE INDEX unique_student_schedule_date ON attendance (student_id, schedule_id, date)');
        $this->addSql('CREATE INDEX IDX_6DE30D91B03A8386 ON attendance (created_by_id)');
        $this->addSql('CREATE INDEX IDX_6DE30D913F3D70EA ON attendance (bimester_id)');
        $this->addSql('CREATE INDEX IDX_6DE30D9141807E1D ON attendance (teacher_id)');
        $this->addSql('CREATE INDEX IDX_6DE30D9123EDC87 ON attendance (subject_id)');
        $this->addSql('CREATE INDEX IDX_6DE30D91A40BC2D5 ON attendance (schedule_id)');
        $this->addSql('CREATE INDEX IDX_6DE30D91CB944F1A ON attendance (student_id)');
        $this->addSql('CREATE TABLE schedule (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, day_of_week INTEGER NOT NULL, period INTEGER NOT NULL, start_time TIME NOT NULL, end_time TIME NOT NULL, classroom VARCHAR(50) DEFAULT NULL COLLATE "BINARY", is_active BOOLEAN NOT NULL, teacher_id INTEGER NOT NULL, subject_id INTEGER NOT NULL, course_id INTEGER NOT NULL, section_id INTEGER DEFAULT NULL, school_cycle_id INTEGER NOT NULL, CONSTRAINT FK_5A3811FB41807E1D FOREIGN KEY (teacher_id) REFERENCES teacher (id) ON UPDATE NO ACTION ON DELETE NO ACTION NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_5A3811FB23EDC87 FOREIGN KEY (subject_id) REFERENCES subject (id) ON UPDATE NO ACTION ON DELETE NO ACTION NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_5A3811FB591CC992 FOREIGN KEY (course_id) REFERENCES course (id) ON UPDATE NO ACTION ON DELETE NO ACTION NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_5A3811FBD823E37A FOREIGN KEY (section_id) REFERENCES section (id) ON UPDATE NO ACTION ON DELETE NO ACTION NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_5A3811FBFBAA2526 FOREIGN KEY (school_cycle_id) REFERENCES school_cycle (id) ON UPDATE NO ACTION ON DELETE NO ACTION NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('CREATE INDEX IDX_5A3811FBFBAA2526 ON schedule (school_cycle_id)');
        $this->addSql('CREATE INDEX IDX_5A3811FBD823E37A ON schedule (section_id)');
        $this->addSql('CREATE INDEX IDX_5A3811FB591CC992 ON schedule (course_id)');
        $this->addSql('CREATE INDEX IDX_5A3811FB23EDC87 ON schedule (subject_id)');
        $this->addSql('CREATE INDEX IDX_5A3811FB41807E1D ON schedule (teacher_id)');
        $this->addSql('DROP TABLE classroom');
        $this->addSql('CREATE TEMPORARY TABLE __temp__audit_log AS SELECT id, "action", entity_type, entity_id, changes, ip_address, user_agent, created_at, user_id FROM audit_log');
        $this->addSql('DROP TABLE audit_log');
        $this->addSql('CREATE TABLE audit_log (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, "action" VARCHAR(50) NOT NULL, entity_type VARCHAR(100) NOT NULL, entity_id INTEGER DEFAULT NULL, changes CLOB DEFAULT NULL, ip_address VARCHAR(45) DEFAULT NULL, user_agent VARCHAR(500) DEFAULT NULL, created_at DATETIME NOT NULL, user_id INTEGER DEFAULT NULL, CONSTRAINT FK_F6E1C0F5A76ED395 FOREIGN KEY (user_id) REFERENCES "user" (id) ON DELETE SET NULL NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('INSERT INTO audit_log (id, "action", entity_type, entity_id, changes, ip_address, user_agent, created_at, user_id) SELECT id, "action", entity_type, entity_id, changes, ip_address, user_agent, created_at, user_id FROM __temp__audit_log');
        $this->addSql('DROP TABLE __temp__audit_log');
        $this->addSql('CREATE INDEX idx_audit_action ON audit_log ("action")');
        $this->addSql('CREATE INDEX idx_audit_entity ON audit_log (entity_type, entity_id)');
        $this->addSql('CREATE INDEX idx_audit_user ON audit_log (user_id)');
        $this->addSql('CREATE INDEX idx_audit_created ON audit_log (created_at)');
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
        $this->addSql('CREATE TEMPORARY TABLE __temp__user AS SELECT id, name, email, roles, password, is_active FROM "user"');
        $this->addSql('DROP TABLE "user"');
        $this->addSql('CREATE TABLE "user" (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, name VARCHAR(100) DEFAULT NULL, email VARCHAR(180) NOT NULL, roles CLOB NOT NULL, password VARCHAR(255) NOT NULL, is_active BOOLEAN DEFAULT 1 NOT NULL)');
        $this->addSql('INSERT INTO "user" (id, name, email, roles, password, is_active) SELECT id, name, email, roles, password, is_active FROM __temp__user');
        $this->addSql('DROP TABLE __temp__user');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_8D93D649E7927C74 ON "user" (email)');
    }
}
