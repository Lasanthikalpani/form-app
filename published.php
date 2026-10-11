<?php
require 'config.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: auth.php');
    exit;
}

// Fetch ONLY approved records
$stmt = $pdo->query("SELECT name, nic, reviewed_at FROM submissions WHERE status = 'approved' ORDER BY reviewed_at DESC");
$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html>
<head>
    <title>Published Records</title>
    <style>
        body { font-family: Arial; max-width: 800px; margin: 40px auto; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #ccc; padding: 10px; }
        th { background: #28a745; color: white; }
        .empty { padding: 20px; background: #fff3cd; }
        .userbar { background: #f8f9fa; padding: 10px; border-radius: 4px; margin-bottom: 15px; text-align: right; font-size: 14px; }
    </style>
</head>
<body>

    <div class="userbar">
        Logged in as <strong><?= htmlspecialchars($_SESSION['username']) ?></strong> |
        <a href="auth.php?logout=1">Logout</a>
    </div>

    <h2>Published (Approved) Records</h2>
    <?php if (empty($rows)): ?>
        <div class="empty">No approved records yet.</div>
    <?php else: ?>
    <table>
        <tr><th>Name</th><th>NIC</th><th>Approved At</th></tr>
        <?php foreach ($rows as $r): ?>
        <tr>
            <td><?= htmlspecialchars($r['name']) ?></td>
            <td><?= htmlspecialchars($r['nic']) ?></td>
            <td><?= htmlspecialchars($r['reviewed_at']) ?></td>
        </tr>
        <?php endforeach; ?>
    </table>
    <?php endif; ?>
    <p><a href="index.php">New Submission</a> | <a href="review.php">Review Panel</a></p>
</body>
</html>