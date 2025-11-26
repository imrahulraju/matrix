<?php
require_once '../includes/auth.php';
requirePermission('manage_users');

$message = '';
$error = '';

// Handle Add User
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'add') {
    $username = trim($_POST['username']);
    $password = $_POST['password'];
    $role = $_POST['role']; // 'editor'
    $permissions = isset($_POST['permissions']) ? $_POST['permissions'] : [];

    if ($username && $password) {
        $users = getUsers();
        // Check if username exists
        $exists = false;
        foreach ($users as $u) {
            if ($u['username'] === $username) {
                $exists = true;
                break;
            }
        }

        if ($exists) {
            $error = 'Username already exists.';
        } else {
            $newUser = [
                'id' => time(),
                'username' => $username,
                'password' => password_hash($password, PASSWORD_DEFAULT),
                'role' => $role,
                'permissions' => $permissions
            ];
            $users[] = $newUser;
            saveUsers($users);
            $message = 'User added successfully.';
        }
    } else {
        $error = 'Username and Password are required.';
    }
}

// Handle Delete User
if (isset($_GET['delete'])) {
    $deleteId = $_GET['delete'];
    $users = getUsers();
    $newUsers = [];
    foreach ($users as $u) {
        if ($u['id'] != $deleteId) {
            $newUsers[] = $u;
        } elseif ($u['role'] === 'superadmin') {
            $error = 'Cannot delete Super Admin.';
            $newUsers[] = $u;
        }
    }
    if (!$error) {
        saveUsers($newUsers);
        $message = 'User deleted successfully.';
    }
}

$users = getUsers();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Users - Tour Matrix</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
        body { background-color: #f8f9fc; padding-top: 80px; }
        .mx-admin-container { max-width: 1000px; margin: 0 auto; padding: 30px 16px; }
        .mx-card { background: white; padding: 30px; border-radius: 12px; box-shadow: 0 4px 20px rgba(0,0,0,0.05); margin-bottom: 30px; }
        .mx-table { width: 100%; border-collapse: collapse; }
        .mx-table th, .mx-table td { padding: 12px; border-bottom: 1px solid #eee; text-align: left; }
        .mx-badge { padding: 4px 8px; border-radius: 4px; font-size: 12px; background: #eee; }
        .mx-badge-perm { background: #e3f2fd; color: #1976d2; margin-right: 4px; }
    </style>
</head>
<body>

<header class="mx-header" style="position: fixed; top: 0; width: 100%; background: white; z-index: 100; height: 70px; display: flex; align-items: center; justify-content: space-between; padding: 0 30px; box-shadow: 0 2px 10px rgba(0,0,0,0.05);">
    <div class="mx-header__logo">
        <img src="../assets/images/logo.png" alt="Tour Matrix" style="height: 40px;">
    </div>
    <div>
        <a href="index.php" class="mx-btn mx-btn--small" style="margin-right: 10px;">Back to Dashboard</a>
        <a href="logout.php" class="mx-btn mx-btn--small" style="background: #333;">Logout</a>
    </div>
</header>

<div class="mx-admin-container">
    <h1 class="mx-title">Manage Users</h1>

    <?php if ($message): ?><p style="color: green;"><?php echo $message; ?></p><?php endif; ?>
    <?php if ($error): ?><p style="color: red;"><?php echo $error; ?></p><?php endif; ?>

    <!-- Add User Form -->
    <div class="mx-card">
        <h3>Create New User</h3>
        <form method="POST">
            <input type="hidden" name="action" value="add">
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 20px;">
                <div>
                    <label style="display: block; margin-bottom: 5px;">Username</label>
                    <input type="text" name="username" class="mx-form-input" required style="width: 100%; padding: 8px;">
                </div>
                <div>
                    <label style="display: block; margin-bottom: 5px;">Password</label>
                    <input type="password" name="password" class="mx-form-input" required style="width: 100%; padding: 8px;">
                </div>
            </div>
            
            <div style="margin-bottom: 20px;">
                <label style="display: block; margin-bottom: 5px;">Role</label>
                <select name="role" style="width: 100%; padding: 8px;">
                    <option value="editor">Editor</option>
                    <option value="superadmin">Super Admin</option>
                </select>
            </div>

            <div style="margin-bottom: 20px;">
                <label style="display: block; margin-bottom: 10px;">Permissions</label>
                <label style="margin-right: 15px;"><input type="checkbox" name="permissions[]" value="create" checked> Create Posts</label>
                <label style="margin-right: 15px;"><input type="checkbox" name="permissions[]" value="update" checked> Edit Posts</label>
                <label style="margin-right: 15px;"><input type="checkbox" name="permissions[]" value="delete"> Delete Posts</label>
                <label style="margin-right: 15px;"><input type="checkbox" name="permissions[]" value="manage_users"> Manage Users</label>
            </div>

            <button type="submit" class="mx-btn">Create User</button>
        </form>
    </div>

    <!-- User List -->
    <div class="mx-card">
        <h3>Existing Users</h3>
        <table class="mx-table">
            <thead>
                <tr>
                    <th>Username</th>
                    <th>Role</th>
                    <th>Permissions</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($users as $user): ?>
                <tr>
                    <td><?php echo htmlspecialchars($user['username']); ?></td>
                    <td><span class="mx-badge"><?php echo htmlspecialchars($user['role']); ?></span></td>
                    <td>
                        <?php foreach ($user['permissions'] as $perm): ?>
                            <span class="mx-badge mx-badge-perm"><?php echo $perm; ?></span>
                        <?php endforeach; ?>
                    </td>
                    <td>
                        <?php if ($user['role'] !== 'superadmin'): ?>
                            <a href="?delete=<?php echo $user['id']; ?>" onclick="return confirm('Delete this user?')" style="color: red;">Delete</a>
                        <?php else: ?>
                            <span style="color: #999;">(Main Admin)</span>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

</body>
</html>
