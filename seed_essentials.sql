-- Essential Seed Data for Oxford System (MySQL compatible)

-- 1. School Cycle 2026
INSERT IGNORE INTO school_cycle (id, name, start_date, end_date, is_active) 
VALUES (1, 'Ciclo Escolar 2026', '2026-01-01', '2026-10-31', 1);

-- 2. Academic Levels
INSERT IGNORE INTO academic_level (id, name, code, sort_order, is_active) VALUES 
(1, 'Pre-Primaria', 'OD-PRE', 1, 1),
(2, 'Primaria', 'OD-PRI', 2, 1),
(3, 'Básico', 'OD-BAS', 3, 1),
(4, 'Diversificado', 'OD-DIV', 4, 1);

-- 3. Grades (Levels)
INSERT IGNORE INTO grade (id, name, level_id, is_active) VALUES
(1, 'Kinder', 1, 1),
(2, 'Preparatoria', 1, 1),
(3, 'Primero Primaria', 2, 1),
(4, 'Segundo Primaria', 2, 1),
(5, 'Tercero Primaria', 2, 1),
(6, 'Cuarto Primaria', 2, 1),
(7, 'Quinto Primaria', 2, 1),
(8, 'Sexto Primaria', 2, 1),
(9, 'Primero Básico', 3, 1),
(10, 'Segundo Básico', 3, 1),
(11, 'Tercero Básico', 3, 1),
(12, 'Cuarto Bachillerato', 4, 1),
(13, 'Quinto Bachillerato', 4, 1);
