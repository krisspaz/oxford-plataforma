<?php
$dbs = [
    'dev' => __DIR__ . '/var/data_dev.db',
    'prod' => __DIR__ . '/var/data_prod.db',
];

foreach ($dbs as $env => $path) {
    if (file_exists($path)) {
        echo "Checking DB: $env ($path)\n";
        try {
            $pdo = new PDO("sqlite:$path");
            $stmt = $pdo->query("SELECT email, roles, password FROM user WHERE email = 'teacher@oxford.edu'");
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($user) {
                echo "Found teacher@oxford.edu:\n";
                echo "Roles: " . $user['roles'] . "\n";
                echo "Password Hash (start): " . substr($user['password'], 0, 10) . "...\n";
            } else {
                echo "User teacher@oxford.edu NOT FOUND.\n";
            }
        } catch (Exception $e) {
            echo "Error opening DB: " . $e->getMessage() . "\n";
        }
    } else {
        echo "DB not found: $env ($path)\n";
    }
    echo "-------------------\n";
}
