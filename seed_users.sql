-- Seed Users for Oxford System (MySQL compatible)
-- Password for all users: oxford123

-- Clean up first to ensure idempotency
DELETE FROM staff WHERE id IN (SELECT id FROM person WHERE user_id IN (SELECT id FROM `user` WHERE email IN ('coordinacion@ejemplo.com', 'direccion@ejemplo.com')));
DELETE FROM teacher WHERE id IN (SELECT id FROM person WHERE user_id IN (SELECT id FROM `user` WHERE email = 'docente@ejemplo.com'));
DELETE FROM person WHERE user_id IN (SELECT id FROM `user` WHERE email IN ('coordinacion@ejemplo.com', 'direccion@ejemplo.com', 'docente@ejemplo.com'));
DELETE FROM `user` WHERE email IN ('coordinacion@ejemplo.com', 'direccion@ejemplo.com', 'docente@ejemplo.com');

-- 1. Coordinacion
INSERT INTO `user` (email, password, roles, is_active, name, two_factor_auth_enabled)
VALUES (
    'coordinacion@ejemplo.com', 
    '$2y$10$Adxr42DorkVQjTwVvhG8RODuKkNzIeCF0.fOSRRxX9H4dfMzMCQOq',
    '["ROLE_COORDINACION"]', 
    1, 
    'Coordinador General',
    0
);

SET @coord_user_id = LAST_INSERT_ID();

INSERT INTO person (first_name, last_name, is_active, created_at, user_id, person_type)
VALUES ('Coordinador', 'General', 1, NOW(), @coord_user_id, 'staff');

SET @coord_person_id = LAST_INSERT_ID();

INSERT INTO staff (id, position, contract_type)
VALUES (@coord_person_id, 'Coordinador Academico', 'Tiempo Completo');

-- 2. Direccion
INSERT INTO `user` (email, password, roles, is_active, name, two_factor_auth_enabled)
VALUES (
    'direccion@ejemplo.com', 
    '$2y$10$Adxr42DorkVQjTwVvhG8RODuKkNzIeCF0.fOSRRxX9H4dfMzMCQOq',
    '["ROLE_DIRECCION"]', 
    1, 
    'Director General',
    0
);

SET @dir_user_id = LAST_INSERT_ID();

INSERT INTO person (first_name, last_name, is_active, created_at, user_id, person_type)
VALUES ('Ana', 'Directora', 1, NOW(), @dir_user_id, 'staff');

SET @dir_person_id = LAST_INSERT_ID();

INSERT INTO staff (id, position, contract_type)
VALUES (@dir_person_id, 'Directora General', 'Tiempo Completo');

-- 3. Docente
INSERT INTO `user` (email, password, roles, is_active, name, two_factor_auth_enabled)
VALUES (
    'docente@ejemplo.com', 
    '$2y$10$Adxr42DorkVQjTwVvhG8RODuKkNzIeCF0.fOSRRxX9H4dfMzMCQOq',
    '["ROLE_DOCENTE"]', 
    1, 
    'Carlos Docente',
    0
);

SET @doc_user_id = LAST_INSERT_ID();

INSERT INTO person (first_name, last_name, is_active, created_at, user_id, person_type)
VALUES ('Carlos', 'Docente', 1, NOW(), @doc_user_id, 'teacher');

SET @doc_person_id = LAST_INSERT_ID();

INSERT INTO teacher (id, contract_type, employee_code)
VALUES (@doc_person_id, 'Tiempo Completo', 'DOC-001');
