<?php
// auth.php - Registration + Login page
require 'config.php';

session_start();

$msg = '';
$activeTab = $_GET['tab'] ?? 'login';
if (!in_array($activeTab, ['login', 'register'])) {
    $activeTab = 'login';
}

// Handle Logout
if (isset($_GET['logout'])) {
    $_SESSION = [];
    if (ini_get("session.use_cookies")) {
        $params = session_get_cookie_params();
        setcookie(
            session_name(), '',
            time() - 42000,
            $params["path"], $params["domain"],
            $params["secure"], $params["httponly"]
        );
    }
    session_destroy();
    header('Location: auth.php?tab=login');
    exit;
}

// Redirect if already logged in (role-aware)
if (isset($_SESSION['user_id'])) {
    header('Location: ' . ($_SESSION['role'] === 'admin' ? 'review.php' : 'index.php'));
    exit;
}

// Handle Registration (customers only)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['register'])) {
    $activeTab = 'register';
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm  = $_POST['confirm'] ?? '';

    if ($username === '' || $password === '' || $confirm === '') {
        $msg = "All fields are required.";
    } elseif ($password !== $confirm) {
        $msg = "Passwords do not match.";
    } elseif (strlen($password) < 4) {
        $msg = "Password must be at least 4 characters.";
    } else {
        $stmt = $pdo->prepare("SELECT id FROM users WHERE username = ?");
        $stmt->execute([$username]);
        if ($stmt->fetch()) {
            $msg = "Username already taken.";
        } else {
            $hash = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("INSERT INTO users (username, password_hash, role) VALUES (?, ?, 'customer')");
            $stmt->execute([$username, $hash]);
            $msg = "Registration successful! Please log in.";
            $activeTab = 'login';
        }
    }
}

// Handle Login
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['login'])) {
    $activeTab = 'login';
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($username === '' || $password === '') {
        $msg = "Both fields are required.";
    } else {
        $stmt = $pdo->prepare("SELECT id, username, password_hash, role FROM users WHERE username = ?");
        $stmt->execute([$username]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && password_verify($password, $user['password_hash'])) {
            $_SESSION['user_id']  = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['role']     = $user['role'];

            // Role එක අනුව redirect
            if ($user['role'] === 'admin') {
                header('Location: review.php');
            } else {
                header('Location: index.php');
            }
            exit;
        } else {
            $msg = "Invalid username or password.";
        }
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Login / Register</title>
    <style>
        body { font-family: Arial; max-width: 420px; margin: 50px auto; background: #f8f9fa; }
        .card { background: white; padding: 30px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        h2 { text-align: center; margin-bottom: 20px; color: #333; }
        .tabs { display: flex; margin-bottom: 20px; border-bottom: 2px solid #ddd; }
        .tabs a { flex: 1; text-align: center; padding: 10px; text-decoration: none; color: #555; font-weight: bold; }
        .tabs a.active { color: #007bff; border-bottom: 2px solid #007bff; margin-bottom: -2px; }
        input, button { padding: 12px; width: 100%; margin: 6px 0; box-sizing: border-box; border: 1px solid #ccc; border-radius: 4px; font-size: 14px; }
        button { background: #007bff; color: white; border: none; cursor: pointer; font-size: 16px; font-weight: bold; }
        button:hover { background: #0056b3; }
        .msg { padding: 12px; margin-bottom: 15px; border-radius: 4px; font-size: 14px; }
        .msg.error { background: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }
        .msg.success { background: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
    </style>
</head>
<body>
    <div class="card">
        <h2>🔐 User Access</h2>

        <div class="tabs">
            <a href="?tab=login" class="<?= $activeTab === 'login' ? 'active' : '' ?>">Login</a>
            <a href="?tab=register" class="<?= $activeTab === 'register' ? 'active' : '' ?>">Register</a>
        </div>

        <?php if ($msg): ?>
            <div class="msg <?= (strpos($msg, 'successful') !== false) ? 'success' : 'error' ?>">
                <?= htmlspecialchars($msg) ?>
            </div>
        <?php endif; ?>

        <?php if ($activeTab === 'login'): ?>
            <form method="POST">
                <input type="text" name="username" placeholder="Username" required autofocus>
                <input type="password" name="password" placeholder="Password" required>
                <button type="submit" name="login">Login</button>
            </form>
            <p style="text-align:center; margin-top:15px; font-size:13px; color:#666;">
                Don't have an account? <a href="?tab=register">Register here</a>
            </p>
        <?php else: ?>
            <form method="POST">
                <input type="text" name="username" placeholder="Username" required autofocus>
                <input type="password" name="password" placeholder="Password (min 4 chars)" required>
                <input type="password" name="confirm" placeholder="Confirm Password" required>
                <button type="submit" name="register">Create Account</button>
            </form>
            <p style="text-align:center; margin-top:15px; font-size:13px; color:#666;">
                Already have an account? <a href="?tab=login">Login here</a>
            </p>
        <?php endif; ?>
    </div>
</body>
</html>