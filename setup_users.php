<?php
$usersFile = 'assets/data/users.json';

// Default Super Admin
$users = [
    [
        'id' => 1,
        'username' => 'superadmin',
        'password' => password_hash('admin123', PASSWORD_DEFAULT),
        'role' => 'superadmin',
        'permissions' => ['create', 'read', 'update', 'delete', 'manage_users']
    ]
];

if (!file_exists(dirname($usersFile))) {
    mkdir(dirname($usersFile), 0777, true);
}

file_put_contents($usersFile, json_encode($users, JSON_PRETTY_PRINT));

echo "Users file created with default superadmin (password: admin123)\n";
?>
