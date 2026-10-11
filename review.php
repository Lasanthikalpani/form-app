<?php
require 'config.php';
$rows = $pdo->query("SELECT * FROM submissions ORDER BY created_at DESC")->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html>
<head>
    <title>Review Submissions</title>
    <style>
        body { font-family: Arial; max-width: 800px; margin: 40px auto; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #ccc; padding: 10px; text-align: left; }
        th { background: #f4f4f4; }
        .btn { padding: 6px 12px; text-decoration: none; color: white; border-radius: 4px; margin-right: 5px; }
        .approve { background: green; }
        .reject { background: red; }
        .pending { color: orange; font-weight: bold; }
        .approved { color: green; font-weight: bold; }
        .rejected { color: red; font-weight: bold; }
    </style>
</head>
<body>
    <h2>Review Submissions</h2>
    <table>
        <tr><th>ID</th><th>Name</th><th>NIC</th><th>Status</th><th>Action</th></tr>
        <?php foreach ($rows as $r): ?>
        <tr>
            <td><?= $r['id'] ?></td>
            <td><?= htmlspecialchars($r['name']) ?></td>
            <td><?= htmlspecialchars($r['nic']) ?></td>
            <td class="<?= $r['status'] ?>"><?= ucfirst($r['status']) ?></td>
            <td>
                <?php if ($r['status'] === 'pending'): ?>
                    <a class="btn approve" href="actions.php?id=<?= $r['id'] ?>&action=approve">Approve</a>
                    <a class="btn reject" href="actions.php?id=<?= $r['id'] ?>&action=reject">Reject</a>
                <?php else: ?>
                    —
                <?php endif; ?>
            </td>
        </tr>
        <?php endforeach; ?>
    </table>
    <p><a href="index.php">New Submission</a> | <a href="published.php">View Published</a></p>
</body>
</html>