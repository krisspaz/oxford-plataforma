<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260122_Optimization extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Adds performance indexes for Student, Enrollment, GradeRecord, Payment, and AuditLog';
    }

    public function up(Schema $schema): void
    {
        // Student indexes - Already applied in Version20260122_Indexes
        // $this->addSql('CREATE INDEX idx_student_active ON student(is_active) WHERE is_active = true');

        // Enrollment indexes - Already applied
        // $this->addSql('CREATE INDEX idx_enrollment_student_cycle ON enrollment(student_id, school_cycle_id)');
        // $this->addSql('CREATE INDEX idx_enrollment_grade_section ON enrollment(grade_id, section_id)');

        // Grade record indexes 
        // Adapting subject_id -> subject_assignment_id (This was skipped in previous run)
        $this->addSql('CREATE INDEX idx_grade_record_lookup ON grade_record(student_id, bimester_id, subject_assignment_id)');
        
        // idx_grade_record_teacher (entered_by_id) - Already applied

        // Payment indexes - Already applied
        // $this->addSql('CREATE INDEX idx_payment_status_date ON payment(status, due_date)');
        // $this->addSql('CREATE INDEX idx_payment_student_pending ON payment(student_id, status) WHERE status = \'PENDING\'');

        // Audit log indexes - Already applied
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP INDEX idx_student_active');
        $this->addSql('DROP INDEX idx_enrollment_student_cycle');
        $this->addSql('DROP INDEX idx_enrollment_grade_section');
        $this->addSql('DROP INDEX idx_grade_record_lookup');
        $this->addSql('DROP INDEX idx_grade_record_teacher');
        $this->addSql('DROP INDEX idx_payment_status_date');
        $this->addSql('DROP INDEX idx_payment_student_pending');
        $this->addSql('DROP INDEX idx_audit_log_entity');
        $this->addSql('DROP INDEX idx_audit_log_user');
    }
}
