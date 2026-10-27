<?php
require_once '../db.php';

$id = $_GET['id'] ?? '';

if ($id) {
    try {
        $stmt = $pdo->prepare("DELETE FROM passenger WHERE passenger_id = ?");
        $stmt->execute([$id]);
    } catch (PDOException $e) {
        die("<script>alert('Cannot delete this passenger because they have active reservations or associated records.'); window.location.href='index.php';</script>");
    }
}

header("Location: index.php");
exit;
?>