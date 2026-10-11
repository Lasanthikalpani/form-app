<?php
require 'config.php';
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
    </style>
</head>
<body>
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