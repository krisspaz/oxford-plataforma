-- Essential Seed Data for Oxford System

-- 1. School Cycle 2026
INSERT INTO school_cycle (id, name, start_date, end_date, is_active) 
VALUES (1, 'Ciclo Escolar 2026', '2026-01-01', '2026-10-31', true)
ON CONFLICT (id) DO NOTHING;

-- 2. Academic Levels
-- name, code, sort_order, is_active
INSERT INTO academic_level (id, name, code, sort_order, is_active) VALUES 
(1, 'Pre-Primaria', 'OD-PRE', 1, true),
(2, 'Primaria', 'OD-PRI', 2, true),
(3, 'Básico', 'OD-BAS', 3, true),
(4, 'Diversificado', 'OD-DIV', 4, true)
ON CONFLICT (id) DO NOTHING;

-- 3. Grades (Levels)
-- name, level_id, is_active
INSERT INTO grade (id, name, level_id, is_active) VALUES
(1, 'Kinder', 1, true),
(2, 'Preparatoria', 1, true),
(3, 'Primero Primaria', 2, true),
(4, 'Segundo Primaria', 2, true),
(5, 'Tercero Primaria', 2, true),
(6, 'Cuarto Primaria', 2, true),
(7, 'Quinto Primaria', 2, true),
(8, 'Sexto Primaria', 2, true),
(9, 'Primero Básico', 3, true),
(10, 'Segundo Básico', 3, true),
(11, 'Tercero Básico', 3, true),
(12, 'Cuarto Bachillerato', 4, true),
(13, 'Quinto Bachillerato', 4, true)
ON CONFLICT (id) DO NOTHING;

-- Reset sequences
SELECT setval('school_cycle_id_seq', (SELECT MAX(id) FROM school_cycle));
SELECT setval('academic_level_id_seq', (SELECT MAX(id) FROM academic_level));
SELECT setval('grade_id_seq', (SELECT MAX(id) FROM grade));
