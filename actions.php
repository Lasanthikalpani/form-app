<?php
require 'config.php';
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: auth.php');
    exit;
}

$id     = (int)($_GET['id'] ?? 0);
$action = $_GET['action'] ?? '';

if ($id > 0 && in_array($action, ['approve', 'reject'])) {
    $status = $action === 'approve' ? 'approved' : 'rejected';
    $stmt = $pdo->prepare("UPDATE submissions SET status = ?, reviewed_at = NOW() WHERE id = ? AND status = 'pending'");
    $stmt->execute([$status, $id]);
}

header('Location: review.php');
exit;