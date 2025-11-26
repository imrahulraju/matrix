<?php
session_start();

define('USERS_FILE', __DIR__ . '/../assets/data/users.json');

function getUsers() {
    if (file_exists(USERS_FILE)) {
        return json_decode(file_get_contents(USERS_FILE), true);
    }
    return [];
}

function saveUsers($users) {
    file_put_contents(USERS_FILE, json_encode($users, JSON_PRETTY_PRINT));
}

function login($username, $password) {
    $users = getUsers();
    foreach ($users as $user) {
        if ($user['username'] === $username && password_verify($password, $user['password'])) {
            $_SESSION['admin_logged_in'] = true;
            $_SESSION['admin_user_id'] = $user['id'];
            $_SESSION['admin_username'] = $user['username'];
            $_SESSION['admin_role'] = $user['role'];
            $_SESSION['admin_permissions'] = $user['permissions'];
            return true;
        }
    }
    return false;
}

function logout() {
    session_destroy();
}

function isLoggedIn() {
    return isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true;
}

function requireLogin() {
    if (!isLoggedIn()) {
        header("Location: login.php");
        exit;
    }
}

function hasPermission($permission) {
    if (!isLoggedIn()) return false;
    // Superadmin has all permissions
    if (isset($_SESSION['admin_role']) && $_SESSION['admin_role'] === 'superadmin') return true;
    
    $perms = $_SESSION['admin_permissions'] ?? [];
    return in_array($permission, $perms);
}

function requirePermission($permission) {
    requireLogin();
    if (!hasPermission($permission)) {
        die("Access Denied: You do not have permission to perform this action.");
    }
}
?>
