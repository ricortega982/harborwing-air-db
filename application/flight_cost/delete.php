<?php
require_once '../db.php';

$id = $_GET['id'] ?? '';

if ($id) {
    try {
        $stmt = $pdo->prepare("DELETE FROM flight_cost WHERE cost_id = ?");
        $stmt->execute([$id]);
    } catch (PDOException $e) {
        die("<script>alert('Cannot delete this cost record.'); window.location.href='index.php';</script>");
    }
}

header("Location: index.php");
exit;
?>