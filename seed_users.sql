-- Clean up first to ensure idempotency
DELETE FROM person WHERE user_id IN (
    SELECT id FROM "user" WHERE email IN (
        'coordinacion@ejemplo.com', 
        'direccion@ejemplo.com', 
        'docente@ejemplo.com'
    )
);

DELETE FROM "user" WHERE email IN (
    'coordinacion@ejemplo.com', 
    'direccion@ejemplo.com', 
    'docente@ejemplo.com'
);

-- 1. Coordinacion
WITH new_user AS (
    INSERT INTO "user" (email, password, roles, is_active, name, two_factor_auth_enabled)
    VALUES (
        'coordinacion@ejemplo.com', 
        '$2y$10$Adxr42DorkVQjTwVvhG8RODuKkNzIeCF0.fOSRRxX9H4dfMzMCQOq', -- oxford123
        '["ROLE_COORDINACION"]', 
        true, 
        'Coordinador General',
        false
    )
    RETURNING id
),
new_person AS (
    INSERT INTO person (first_name, last_name, is_active, created_at, user_id, person_type)
    SELECT 'Coordinador', 'General', true, NOW(), id, 'staff'
    FROM new_user
    RETURNING id
)
INSERT INTO staff (id, position, contract_type)
SELECT id, 'Coordinador Academico', 'Tiempo Completo'
FROM new_person;

-- 2. Direccion
WITH new_user AS (
    INSERT INTO "user" (email, password, roles, is_active, name, two_factor_auth_enabled)
    VALUES (
        'direccion@ejemplo.com', 
        '$2y$10$Adxr42DorkVQjTwVvhG8RODuKkNzIeCF0.fOSRRxX9H4dfMzMCQOq', -- oxford123
        '["ROLE_DIRECCION"]', 
        true, 
        'Director General',
        false
    )
    RETURNING id
),
new_person AS (
    INSERT INTO person (first_name, last_name, is_active, created_at, user_id, person_type)
    SELECT 'Ana', 'Directora', true, NOW(), id, 'staff'
    FROM new_user
    RETURNING id
)
INSERT INTO staff (id, position, contract_type)
SELECT id, 'Directora General', 'Tiempo Completo'
FROM new_person;

-- 3. Docente
WITH new_user AS (
    INSERT INTO "user" (email, password, roles, is_active, name, two_factor_auth_enabled)
    VALUES (
        'docente@ejemplo.com', 
        '$2y$10$Adxr42DorkVQjTwVvhG8RODuKkNzIeCF0.fOSRRxX9H4dfMzMCQOq', -- oxford123
        '["ROLE_DOCENTE"]', 
        true, 
        'Carlos Docente',
        false
    )
    RETURNING id
),
new_person AS (
    INSERT INTO person (first_name, last_name, is_active, created_at, user_id, person_type)
    SELECT 'Carlos', 'Docente', true, NOW(), id, 'teacher'
    FROM new_user
    RETURNING id
)
INSERT INTO teacher (id, contract_type, employee_code)
SELECT id, 'Tiempo Completo', 'DOC-001'
FROM new_person;
