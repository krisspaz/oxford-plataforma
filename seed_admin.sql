-- Insert global admin user for login test (MySQL compatible)
-- Password: oxford123

-- First, insert the user (skip if email already exists)
INSERT IGNORE INTO `user` (email, password, roles, is_active, name, two_factor_auth_enabled)
VALUES (
    'admin@oxford.edu', 
    '$2y$10$Adxr42DorkVQjTwVvhG8RODuKkNzIeCF0.fOSRRxX9H4dfMzMCQOq',
    '["ROLE_ADMIN", "ROLE_SUPER_ADMIN"]', 
    1, 
    'Super Admin',
    0
);

-- Then, insert the person record (only if user was created)
INSERT IGNORE INTO person (first_name, last_name, is_active, created_at, user_id, person_type)
SELECT 'Super', 'Admin', 1, NOW(), id, 'staff'
FROM `user` 
WHERE email = 'admin@oxford.edu'
AND NOT EXISTS (
    SELECT 1 FROM person WHERE user_id = (SELECT id FROM `user` WHERE email = 'admin@oxford.edu')
);
