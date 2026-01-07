-- Performance Indexes for Oxford Plataforma
-- ==========================================
-- Run this migration to add indexes for common queries

-- Student queries
CREATE INDEX IF NOT EXISTS idx_student_grade ON student(grade_id);
CREATE INDEX IF NOT EXISTS idx_student_section ON student(section_id);
CREATE INDEX IF NOT EXISTS idx_student_family ON student(family_id);
CREATE INDEX IF NOT EXISTS idx_student_carnet ON student(carnet);
CREATE INDEX IF NOT EXISTS idx_student_active ON student(is_active);

-- User/Auth queries
CREATE INDEX IF NOT EXISTS idx_user_email ON "user"(email);
CREATE INDEX IF NOT EXISTS idx_user_active ON "user"(is_active);

-- Enrollment queries
CREATE INDEX IF NOT EXISTS idx_enrollment_student ON enrollment(student_id);
CREATE INDEX IF NOT EXISTS idx_enrollment_cycle ON enrollment(school_cycle_id);
CREATE INDEX IF NOT EXISTS idx_enrollment_status ON enrollment(status);

-- Payment queries
CREATE INDEX IF NOT EXISTS idx_payment_student ON payment(student_id);
CREATE INDEX IF NOT EXISTS idx_payment_date ON payment(payment_date);
CREATE INDEX IF NOT EXISTS idx_payment_status ON payment(status);

-- Invoice queries
CREATE INDEX IF NOT EXISTS idx_invoice_family ON invoice(family_id);
CREATE INDEX IF NOT EXISTS idx_invoice_status ON invoice(status);
CREATE INDEX IF NOT EXISTS idx_invoice_due_date ON invoice(due_date);

-- Attendance queries
CREATE INDEX IF NOT EXISTS idx_attendance_student ON attendance(student_id);
CREATE INDEX IF NOT EXISTS idx_attendance_date ON attendance(date);
CREATE INDEX IF NOT EXISTS idx_attendance_status ON attendance(status);

-- Grade records
CREATE INDEX IF NOT EXISTS idx_grade_record_student ON grade_record(student_id);
CREATE INDEX IF NOT EXISTS idx_grade_record_subject ON grade_record(subject_id);
CREATE INDEX IF NOT EXISTS idx_grade_record_bimester ON grade_record(bimester_id);

-- Task submissions
CREATE INDEX IF NOT EXISTS idx_task_submission_student ON task_submission(student_id);
CREATE INDEX IF NOT EXISTS idx_task_submission_task ON task_submission(task_id);

-- Schedules
CREATE INDEX IF NOT EXISTS idx_schedule_teacher ON schedule(teacher_id);
CREATE INDEX IF NOT EXISTS idx_schedule_section ON schedule(section_id);
CREATE INDEX IF NOT EXISTS idx_schedule_day ON schedule(day_of_week);

-- Audit logs
CREATE INDEX IF NOT EXISTS idx_audit_user ON audit_log(user_id);
CREATE INDEX IF NOT EXISTS idx_audit_action ON audit_log(action);
CREATE INDEX IF NOT EXISTS idx_audit_entity ON audit_log(entity_type, entity_id);
CREATE INDEX IF NOT EXISTS idx_audit_date ON audit_log(created_at);

-- Refresh tokens
CREATE INDEX IF NOT EXISTS idx_refresh_token_user ON refresh_token(user_id);
CREATE INDEX IF NOT EXISTS idx_refresh_token_expires ON refresh_token(expires_at);

-- Composite indexes for common joins
CREATE INDEX IF NOT EXISTS idx_enrollment_student_cycle ON enrollment(student_id, school_cycle_id);
CREATE INDEX IF NOT EXISTS idx_grade_record_student_bimester ON grade_record(student_id, bimester_id);
CREATE INDEX IF NOT EXISTS idx_payment_student_date ON payment(student_id, payment_date);
