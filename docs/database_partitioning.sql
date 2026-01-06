-- Database Partitioning Strategy (PostgreSQL)
-- Optimized for large datasets: Attendance and Grades

-- ==========================================
-- ATTENDANCE PARTITIONING (By Range/Month)
-- ==========================================

-- 1. Create parent table
CREATE TABLE attendance_partitioned (
    id SERIAL,
    student_id INT NOT NULL,
    date DATE NOT NULL,
    status VARCHAR(20) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id, date)
) PARTITION BY RANGE (date);

-- 2. Create partitions for 2026
CREATE TABLE attendance_2026_01 PARTITION OF attendance_partitioned
    FOR VALUES FROM ('2026-01-01') TO ('2026-02-01');

CREATE TABLE attendance_2026_02 PARTITION OF attendance_partitioned
    FOR VALUES FROM ('2026-02-01') TO ('2026-03-01');

CREATE TABLE attendance_2026_03 PARTITION OF attendance_partitioned
    FOR VALUES FROM ('2026-03-01') TO ('2026-04-01');

-- Index on each partition is automatic, but we can add specific ones
CREATE INDEX idx_attendance_student_date ON attendance_partitioned (student_id, date);

-- ==========================================
-- GRADES PARTITIONING (By List/Period)
-- ==========================================

-- 1. Create parent table
CREATE TABLE grades_partitioned (
    id SERIAL,
    student_id INT NOT NULL,
    subject_id INT NOT NULL,
    period VARCHAR(10) NOT NULL,
    grade DECIMAL(5,2),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id, period)
) PARTITION BY LIST (period);

-- 2. Create partitions for academic periods
CREATE TABLE grades_p1 PARTITION OF grades_partitioned
    FOR VALUES IN ('1', 'Q1', 'B1');

CREATE TABLE grades_p2 PARTITION OF grades_partitioned
    FOR VALUES IN ('2', 'Q2', 'B2');

CREATE TABLE grades_p3 PARTITION OF grades_partitioned
    FOR VALUES IN ('3', 'Q3', 'B3');

CREATE TABLE grades_p4 PARTITION OF grades_partitioned
    FOR VALUES IN ('4', 'Q4', 'B4');

CREATE INDEX idx_grades_student_subject ON grades_partitioned (student_id, subject_id);
