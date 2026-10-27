<?php
require_once '../db.php';

$flight_number = $_GET['flight_number'] ?? '';

if ($flight_number) {
    try {
        $stmt = $pdo->prepare("DELETE FROM flight_schedule WHERE flight_number = ?");
        $stmt->execute([$flight_number]);
    } catch (PDOException $e) {
        die("<script>alert('Cannot delete this flight schedule because it has dependent flight legs or records.'); window.location.href='index.php';</script>");
    }
}

header("Location: index.php");
exit;
?>