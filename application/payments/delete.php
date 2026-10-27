<?php
require_once '../db.php';

$id = $_GET['id'] ?? '';

if ($id) {
    try {
        $stmt = $pdo->prepare("DELETE FROM payment WHERE payment_id = ?");
        $stmt->execute([$id]);
    } catch (PDOException $e) {
        die("<script>alert('Cannot delete this payment because it is linked to a reservation.'); window.location.href='index.php';</script>");
    }
}

header("Location: index.php");
exit;
?>