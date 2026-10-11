<?php
require 'config.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: auth.php');
    exit;
}

// Both customers AND admins can access this page.
// No role check here.

$msg = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $nic  = trim($_POST['nic'] ?? '');

    if ($name === '' || $nic === '') {
        $msg = "Both fields are required.";
    } else {
        $stmt = $pdo->prepare("INSERT INTO submissions (name, nic) VALUES (?, ?)");
        $stmt->execute([$name, $nic]);
        $msg = "Submitted successfully! Awaiting review.";
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Submit Form</title>
    <style>
        body { font-family: Arial; max-width: 500px; margin: 50px auto; }
        input, button { padding: 10px; width: 100%; margin: 5px 0; box-sizing: border-box; }
        button { background: #007bff; color: white; border: none; cursor: pointer; }
        .msg { padding: 10px; background: #e7f3ff; margin-bottom: 15px; }
        .userbar { background: #f8f9fa; padding: 10px; border-radius: 4px; margin-bottom: 15px; text-align: right; font-size: 14px; }
        .nav { margin-top: 20px; font-size: 14px; }
        .nav a { margin-right: 10px; }
    </style>
</head>
<body>

    <div class="userbar">
        Logged in as <strong><?= htmlspecialchars($_SESSION['username']) ?></strong>
        (<?= htmlspecialchars($_SESSION['role']) ?>) |
        <a href="auth.php?logout=1">Logout</a>
    </div>

    <h2>Submit Your Details</h2>
    <?php if ($msg): ?><div class="msg"><?= htmlspecialchars($msg) ?></div><?php endif; ?>
    <form method="POST">
        <input type="text" name="name" placeholder="Name" required>
        <input type="text" name="nic" placeholder="NIC" required>
        <button type="submit">Submit</button>
    </form>

    <div class="nav">
        <?php if ($_SESSION['role'] === 'admin'): ?>
            <a href="review.php">Review Panel</a>
            <a href="published.php">View Published</a>
        <?php else: ?>
            <a href="published.php">View Published</a>
        <?php endif; ?>
    </div>

</body>
</html>