<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Add performance indices for commonly queried fields
 */
final class Version20241216AddPerformanceIndices extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add performance indices for commonly queried fields';
    }

    public function up(Schema $schema): void
    {
        // User indices
        $this->addSql('CREATE INDEX IF NOT EXISTS idx_user_email ON "user" (email)');
        $this->addSql('CREATE INDEX IF NOT EXISTS idx_user_is_active ON "user" (is_active)');
        
        // Student indices
        $this->addSql('CREATE INDEX IF NOT EXISTS idx_student_code ON student (student_code)');
        $this->addSql('CREATE INDEX IF NOT EXISTS idx_student_is_active ON student (is_active)');
        $this->addSql('CREATE INDEX IF NOT EXISTS idx_student_last_name ON student (last_name)');
        
        // Teacher indices
        $this->addSql('CREATE INDEX IF NOT EXISTS idx_teacher_employee_code ON teacher (employee_code)');
        $this->addSql('CREATE INDEX IF NOT EXISTS idx_teacher_is_active ON teacher (is_active)');
        
        // Enrollment indices
        $this->addSql('CREATE INDEX IF NOT EXISTS idx_enrollment_student ON enrollment (student_id)');
        $this->addSql('CREATE INDEX IF NOT EXISTS idx_enrollment_grade ON enrollment (grade_id)');
        $this->addSql('CREATE INDEX IF NOT EXISTS idx_enrollment_cycle ON enrollment (school_cycle_id)');
        
        // Attendance indices
        $this->addSql('CREATE INDEX IF NOT EXISTS idx_attendance_student ON attendance (student_id)');
        $this->addSql('CREATE INDEX IF NOT EXISTS idx_attendance_date ON attendance (date)');
        $this->addSql('CREATE INDEX IF NOT EXISTS idx_attendance_status ON attendance (status)');
        
        // Task indices
        $this->addSql('CREATE INDEX IF NOT EXISTS idx_task_teacher ON task (teacher_id)');
        $this->addSql('CREATE INDEX IF NOT EXISTS idx_task_bimester ON task (bimester_id)');
        $this->addSql('CREATE INDEX IF NOT EXISTS idx_task_due_date ON task (due_date)');
        $this->addSql('CREATE INDEX IF NOT EXISTS idx_task_status ON task (status)');
        
        // Schedule indices
        $this->addSql('CREATE INDEX IF NOT EXISTS idx_schedule_teacher ON schedule (teacher_id)');
        $this->addSql('CREATE INDEX IF NOT EXISTS idx_schedule_day ON schedule (day_of_week)');
        $this->addSql('CREATE INDEX IF NOT EXISTS idx_schedule_grade ON schedule (grade_id)');
        
        // GradeRecord indices
        $this->addSql('CREATE INDEX IF NOT EXISTS idx_grade_record_student ON grade_record (student_id)');
        $this->addSql('CREATE INDEX IF NOT EXISTS idx_grade_record_bimester ON grade_record (bimester_id)');
        
        // Invoice indices
        $this->addSql('CREATE INDEX IF NOT EXISTS idx_invoice_student ON invoice (student_id)');
        $this->addSql('CREATE INDEX IF NOT EXISTS idx_invoice_status ON invoice (status)');
        $this->addSql('CREATE INDEX IF NOT EXISTS idx_invoice_date ON invoice (issue_date)');
        
        // Payment indices
        $this->addSql('CREATE INDEX IF NOT EXISTS idx_payment_invoice ON payment (invoice_id)');
        $this->addSql('CREATE INDEX IF NOT EXISTS idx_payment_date ON payment (created_at)');
        
        // RefreshToken indices (already added in entity, but ensure they exist)
        $this->addSql('CREATE INDEX IF NOT EXISTS idx_refresh_token_user ON refresh_token (user_id)');
        $this->addSql('CREATE INDEX IF NOT EXISTS idx_refresh_token_expires ON refresh_token (expires_at)');
    }

    public function down(Schema $schema): void
    {
        // Drop all created indices
        $this->addSql('DROP INDEX IF EXISTS idx_user_email');
        $this->addSql('DROP INDEX IF EXISTS idx_user_is_active');
        $this->addSql('DROP INDEX IF EXISTS idx_student_code');
        $this->addSql('DROP INDEX IF EXISTS idx_student_is_active');
        $this->addSql('DROP INDEX IF EXISTS idx_student_last_name');
        $this->addSql('DROP INDEX IF EXISTS idx_teacher_employee_code');
        $this->addSql('DROP INDEX IF EXISTS idx_teacher_is_active');
        $this->addSql('DROP INDEX IF EXISTS idx_enrollment_student');
        $this->addSql('DROP INDEX IF EXISTS idx_enrollment_grade');
        $this->addSql('DROP INDEX IF EXISTS idx_enrollment_cycle');
        $this->addSql('DROP INDEX IF EXISTS idx_attendance_student');
        $this->addSql('DROP INDEX IF EXISTS idx_attendance_date');
        $this->addSql('DROP INDEX IF EXISTS idx_attendance_status');
        $this->addSql('DROP INDEX IF EXISTS idx_task_teacher');
        $this->addSql('DROP INDEX IF EXISTS idx_task_bimester');
        $this->addSql('DROP INDEX IF EXISTS idx_task_due_date');
        $this->addSql('DROP INDEX IF EXISTS idx_task_status');
        $this->addSql('DROP INDEX IF EXISTS idx_schedule_teacher');
        $this->addSql('DROP INDEX IF EXISTS idx_schedule_day');
        $this->addSql('DROP INDEX IF EXISTS idx_schedule_grade');
        $this->addSql('DROP INDEX IF EXISTS idx_grade_record_student');
        $this->addSql('DROP INDEX IF EXISTS idx_grade_record_bimester');
        $this->addSql('DROP INDEX IF EXISTS idx_invoice_student');
        $this->addSql('DROP INDEX IF EXISTS idx_invoice_status');
        $this->addSql('DROP INDEX IF EXISTS idx_invoice_date');
        $this->addSql('DROP INDEX IF EXISTS idx_payment_invoice');
        $this->addSql('DROP INDEX IF EXISTS idx_payment_date');
        $this->addSql('DROP INDEX IF EXISTS idx_refresh_token_user');
        $this->addSql('DROP INDEX IF EXISTS idx_refresh_token_expires');
    }
}
