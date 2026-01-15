<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260114232745 extends AbstractMigration
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
        $this->addSql('CREATE TEMPORARY TABLE __temp__staff AS SELECT employee_code, position, department, hire_date, contract_type, id, job_title_id FROM staff');
        $this->addSql('DROP TABLE staff');
        $this->addSql('CREATE TABLE staff (employee_code VARCHAR(50) DEFAULT NULL, position VARCHAR(100) NOT NULL, department VARCHAR(100) DEFAULT NULL, hire_date DATE DEFAULT NULL, contract_type VARCHAR(50) NOT NULL, id INTEGER NOT NULL, job_title_id INTEGER DEFAULT NULL, PRIMARY KEY (id), CONSTRAINT FK_426EF392BF396750 FOREIGN KEY (id) REFERENCES person (id) ON UPDATE NO ACTION ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_426EF3926DD822C6 FOREIGN KEY (job_title_id) REFERENCES job_title (id) ON UPDATE NO ACTION ON DELETE NO ACTION NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('INSERT INTO staff (employee_code, position, department, hire_date, contract_type, id, job_title_id) SELECT employee_code, position, department, hire_date, contract_type, id, job_title_id FROM __temp__staff');
        $this->addSql('DROP TABLE __temp__staff');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_426EF392C2CC7ADF ON staff (employee_code)');
        $this->addSql('CREATE INDEX IDX_426EF3926DD822C6 ON staff (job_title_id)');
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
        $this->addSql('CREATE TEMPORARY TABLE __temp__guardian AS SELECT occupation, workplace, work_phone, nit, relationship, id FROM guardian');
        $this->addSql('DROP TABLE guardian');
        $this->addSql('CREATE TABLE guardian (occupation VARCHAR(100) DEFAULT NULL, workplace VARCHAR(100) DEFAULT NULL, work_phone VARCHAR(20) DEFAULT NULL, nit VARCHAR(50) DEFAULT NULL, relationship VARCHAR(50) NOT NULL, id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, CONSTRAINT FK_64486055BF396750 FOREIGN KEY (id) REFERENCES person (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('INSERT INTO guardian (occupation, workplace, work_phone, nit, relationship, id) SELECT occupation, workplace, work_phone, nit, relationship, id FROM __temp__guardian');
        $this->addSql('DROP TABLE __temp__guardian');
        $this->addSql('CREATE TEMPORARY TABLE __temp__staff AS SELECT employee_code, position, department, hire_date, contract_type, job_title_id, id FROM staff');
        $this->addSql('DROP TABLE staff');
        $this->addSql('CREATE TABLE staff (employee_code VARCHAR(50) DEFAULT NULL, position VARCHAR(100) NOT NULL, department VARCHAR(100) DEFAULT NULL, hire_date DATE DEFAULT NULL, contract_type VARCHAR(50) NOT NULL, job_title_id INTEGER DEFAULT NULL, id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, CONSTRAINT FK_426EF3926DD822C6 FOREIGN KEY (job_title_id) REFERENCES job_title (id) NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_426EF392BF396750 FOREIGN KEY (id) REFERENCES person (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('INSERT INTO staff (employee_code, position, department, hire_date, contract_type, job_title_id, id) SELECT employee_code, position, department, hire_date, contract_type, job_title_id, id FROM __temp__staff');
        $this->addSql('DROP TABLE __temp__staff');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_426EF392C2CC7ADF ON staff (employee_code)');
        $this->addSql('CREATE INDEX IDX_426EF3926DD822C6 ON staff (job_title_id)');
        $this->addSql('CREATE TEMPORARY TABLE __temp__teacher AS SELECT employee_code, specialization, hire_date, contract_type, id FROM teacher');
        $this->addSql('DROP TABLE teacher');
        $this->addSql('CREATE TABLE teacher (employee_code VARCHAR(50) DEFAULT NULL, specialization VARCHAR(100) DEFAULT NULL, hire_date DATE DEFAULT NULL, contract_type VARCHAR(50) NOT NULL, id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, CONSTRAINT FK_B0F6A6D5BF396750 FOREIGN KEY (id) REFERENCES person (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('INSERT INTO teacher (employee_code, specialization, hire_date, contract_type, id) SELECT employee_code, specialization, hire_date, contract_type, id FROM __temp__teacher');
        $this->addSql('DROP TABLE __temp__teacher');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_B0F6A6D5C2CC7ADF ON teacher (employee_code)');
    }
}
