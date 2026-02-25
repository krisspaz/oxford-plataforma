-- Create default tenant (Oxford Bilingual School)
INSERT IGNORE INTO tenant (id, name, slug, domain, is_active, created_at, updated_at)
VALUES (1, 'Oxford Bilingual School', 'oxford', 'oxford.edu.gt', 1, NOW(), NOW());

-- Create default school cycle
INSERT IGNORE INTO school_cycle (id, name, start_date, end_date, is_active, tenant_id)
VALUES (1, 'Ciclo 2026', '2026-01-15', '2026-10-31', 1, 1);

-- Create default academic level
INSERT IGNORE INTO academic_level (id, name, sort_order, is_active, tenant_id)
VALUES (1, 'Primaria', 1, 1, 1), (2, 'Secundaria', 2, 1, 1), (3, 'Diversificado', 3, 1, 1);

-- Delete existing users to avoid conflicts
DELETE FROM person WHERE user_id IN (SELECT id FROM `user`);
DELETE FROM `user` WHERE email IN ('admin@oxford.edu', 'coordinacion@ejemplo.com', 'direccion@ejemplo.com', 'docente@ejemplo.com');

-- Insert users (password for all: oxford123)
INSERT INTO `user` (email, password, roles, is_active, name, two_factor_auth_enabled, tenant_id)
VALUES 
('admin@oxford.edu', '$2y$10$Adxr42DorkVQjTwVvhG8RODuKkNzIeCF0.fOSRRxX9H4dfMzMCQOq', '["ROLE_ADMIN", "ROLE_SUPER_ADMIN"]', 1, 'Super Admin', 0, 1),
('coordinacion@ejemplo.com', '$2y$10$Adxr42DorkVQjTwVvhG8RODuKkNzIeCF0.fOSRRxX9H4dfMzMCQOq', '["ROLE_COORDINACION"]', 1, 'Coordinador General', 0, 1),
('direccion@ejemplo.com', '$2y$10$Adxr42DorkVQjTwVvhG8RODuKkNzIeCF0.fOSRRxX9H4dfMzMCQOq', '["ROLE_DIRECCION"]', 1, 'Director General', 0, 1),
('docente@ejemplo.com', '$2y$10$Adxr42DorkVQjTwVvhG8RODuKkNzIeCF0.fOSRRxX9H4dfMzMCQOq', '["ROLE_DOCENTE"]', 1, 'Carlos Docente', 0, 1);

-- Insert person records linked to users
INSERT INTO person (first_name, last_name, is_active, created_at, user_id, person_type, tenant_id)
SELECT 'Super', 'Admin', 1, NOW(), id, 'staff', 1 FROM `user` WHERE email = 'admin@oxford.edu';

INSERT INTO person (first_name, last_name, is_active, created_at, user_id, person_type, tenant_id)
SELECT 'Coordinador', 'General', 1, NOW(), id, 'staff', 1 FROM `user` WHERE email = 'coordinacion@ejemplo.com';

INSERT INTO person (first_name, last_name, is_active, created_at, user_id, person_type, tenant_id)
SELECT 'Ana', 'Directora', 1, NOW(), id, 'staff', 1 FROM `user` WHERE email = 'direccion@ejemplo.com';

INSERT INTO person (first_name, last_name, is_active, created_at, user_id, person_type, tenant_id)
SELECT 'Carlos', 'Docente', 1, NOW(), id, 'teacher', 1 FROM `user` WHERE email = 'docente@ejemplo.com';

-- Insert staff records
INSERT INTO staff (id, position, contract_type)
SELECT p.id, 'Administrador del Sistema', 'Tiempo Completo'
FROM person p JOIN `user` u ON p.user_id = u.id WHERE u.email = 'admin@oxford.edu';

INSERT INTO staff (id, position, contract_type)
SELECT p.id, 'Coordinador Academico', 'Tiempo Completo'
FROM person p JOIN `user` u ON p.user_id = u.id WHERE u.email = 'coordinacion@ejemplo.com';

INSERT INTO staff (id, position, contract_type)
SELECT p.id, 'Directora General', 'Tiempo Completo'
FROM person p JOIN `user` u ON p.user_id = u.id WHERE u.email = 'direccion@ejemplo.com';

-- Insert teacher record
INSERT INTO teacher (id, contract_type, employee_code)
SELECT p.id, 'Tiempo Completo', 'DOC-001'
FROM person p JOIN `user` u ON p.user_id = u.id WHERE u.email = 'docente@ejemplo.com';
