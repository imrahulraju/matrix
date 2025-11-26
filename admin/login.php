<?php
require_once '../includes/auth.php';

if (isLoggedIn()) {
    header("Location: index.php");
    exit;
}

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';

    if (login($username, $password)) {
        header("Location: index.php");
        exit;
    } else {
        $error = 'Invalid username or password';
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login - Tour Matrix</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
        body {
            background-color: #f0f3ff;
            display: flex;
            align-items: center;
            justify-content: center;
            height: 100vh;
            margin: 0;
        }

        .mx-login-card {
            background: white;
            padding: 40px;
            border-radius: 16px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 400px;
        }

        .mx-login-logo {
            text-align: center;
            margin-bottom: 30px;
        }

        .mx-login-logo img {
            max-width: 150px;
        }

        .mx-form-group {
            margin-bottom: 20px;
        }

        .mx-form-label {
            display: block;
            margin-bottom: 8px;
            font-weight: 500;
            color: #1e2339;
        }

        .mx-form-input {
            width: 100%;
            padding: 12px;
            border: 1px solid #c6cce3;
            border-radius: 8px;
            font-size: 16px;
            font-family: inherit;
        }

        .mx-btn-full {
            width: 100%;
            margin-top: 10px;
        }

        .mx-error-msg {
            color: red;
            font-size: 14px;
            margin-bottom: 15px;
            text-align: center;
            display: block;
        }
    </style>
</head>

<body>

    <div class="mx-login-card">
        <div class="mx-login-logo">
            <img src="../assets/images/logo.png" alt="Tour Matrix">
        </div>
        <h2 style="text-align: center; margin-bottom: 20px; color: #1e2339;">Admin Login</h2>

        <?php if ($error): ?>
            <div class="mx-error-msg"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>

        <form method="POST" action="">
            <div class="mx-form-group">
                <label class="mx-form-label">Username</label>
                <input type="text" name="username" class="mx-form-input" placeholder="Enter username" required>
            </div>
            <div class="mx-form-group">
                <label class="mx-form-label">Password</label>
                <input type="password" name="password" class="mx-form-input" placeholder="Enter password" required>
            </div>
            <button type="submit" class="mx-btn mx-btn-full">Login</button>
        </form>
        <p style="text-align: center; margin-top: 20px; font-size: 14px; color: #666;">
            Demo Credentials: superadmin / admin123
        </p>
    </div>

</body>

</html>
