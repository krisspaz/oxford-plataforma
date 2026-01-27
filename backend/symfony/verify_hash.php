<?php
$hash = '$2y$10$Adxr42DorkVQjTwVvhG8RODuKkNzIeCF0.fOSRRxX9H4dfMzMCQOq';
$password = 'oxford123';

echo "Testing hash: $hash\n";
echo "Password: $password\n";

if (password_verify($password, $hash)) {
    echo "MATCH! The seed users have the correct password 'oxford123'.\n";
} else {
    echo "MISMATCH! The seed users CANNOT log in with 'oxford123'.\n";
    echo "Correct hash for 'oxford123' should be: " . password_hash($password, PASSWORD_BCRYPT) . "\n";
}
