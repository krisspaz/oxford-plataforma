<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20251212224036 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE attendance (id SERIAL PRIMARY KEY NOT NULL, date DATE NOT NULL, status VARCHAR(20) NOT NULL, student_id INTEGER NOT NULL, course_id INTEGER DEFAULT NULL, CONSTRAINT FK_6DE30D91CB944F1A FOREIGN KEY (student_id) REFERENCES student (id) NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_6DE30D91591CC992 FOREIGN KEY (course_id) REFERENCES course (id) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('CREATE INDEX IDX_6DE30D91CB944F1A ON attendance (student_id)');
        $this->addSql('CREATE INDEX IDX_6DE30D91591CC992 ON attendance (course_id)');
        $this->addSql('CREATE TABLE course (id SERIAL PRIMARY KEY NOT NULL, name VARCHAR(255) NOT NULL, grade_level VARCHAR(255) NOT NULL, section VARCHAR(10) NOT NULL)');
        $this->addSql('CREATE TABLE grade (id SERIAL PRIMARY KEY NOT NULL, score DOUBLE PRECISION NOT NULL, type VARCHAR(50) NOT NULL, date DATE NOT NULL, student_id INTEGER NOT NULL, subject_id INTEGER NOT NULL, CONSTRAINT FK_595AAE34CB944F1A FOREIGN KEY (student_id) REFERENCES student (id) NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_595AAE3423EDC87 FOREIGN KEY (subject_id) REFERENCES subject (id) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('CREATE INDEX IDX_595AAE34CB944F1A ON grade (student_id)');
        $this->addSql('CREATE INDEX IDX_595AAE3423EDC87 ON grade (subject_id)');
        $this->addSql('CREATE TABLE notification (id SERIAL PRIMARY KEY NOT NULL, title VARCHAR(255) NOT NULL, message TEXT NOT NULL, is_read BOOLEAN NOT NULL, created_at TIMESTAMP NOT NULL, user_id INTEGER NOT NULL, CONSTRAINT FK_BF5476CAA76ED395 FOREIGN KEY (user_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('CREATE INDEX IDX_BF5476CAA76ED395 ON notification (user_id)');
        $this->addSql('CREATE TABLE subject (id SERIAL PRIMARY KEY NOT NULL, name VARCHAR(255) NOT NULL, code VARCHAR(50) NOT NULL)');
        $this->addSql('CREATE TABLE subject_course (subject_id INTEGER NOT NULL, course_id INTEGER NOT NULL, PRIMARY KEY (subject_id, course_id), CONSTRAINT FK_9E87A69E23EDC87 FOREIGN KEY (subject_id) REFERENCES subject (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_9E87A69E591CC992 FOREIGN KEY (course_id) REFERENCES course (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('CREATE INDEX IDX_9E87A69E23EDC87 ON subject_course (subject_id)');
        $this->addSql('CREATE INDEX IDX_9E87A69E591CC992 ON subject_course (course_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE attendance');
        $this->addSql('DROP TABLE course');
        $this->addSql('DROP TABLE grade');
        $this->addSql('DROP TABLE notification');
        $this->addSql('DROP TABLE subject');
        $this->addSql('DROP TABLE subject_course');
    }
}
