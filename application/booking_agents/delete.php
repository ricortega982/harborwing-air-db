<?php
require_once '../db.php';

$id = $_GET['id'] ?? '';

if ($id) {
    try {
        $stmt = $pdo->prepare("DELETE FROM booking_agent WHERE agent_id = ?");
        $stmt->execute([$id]);
    } catch (PDOException $e) {
        die("<script>alert('Cannot delete this agent because they are associated with existing reservations.'); window.location.href='index.php';</script>");
    }
}

header("Location: index.php");
exit;
?>