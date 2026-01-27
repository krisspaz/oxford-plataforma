-- Insert global admin user for login test
WITH new_user AS (
    INSERT INTO "user" (email, password, roles, is_active, name, two_factor_auth_enabled)
    VALUES (
        'admin@oxford.edu', 
        '$2y$10$Adxr42DorkVQjTwVvhG8RODuKkNzIeCF0.fOSRRxX9H4dfMzMCQOq', -- oxford123
        '["ROLE_ADMIN", "ROLE_SUPER_ADMIN"]', 
        true, 
        'Super Admin',
        false
    )
    ON CONFLICT (email) DO NOTHING
    RETURNING id
)
INSERT INTO person (first_name, last_name, is_active, created_at, user_id, person_type)
SELECT 'Super', 'Admin', true, NOW(), id, 'staff'
FROM new_user;
