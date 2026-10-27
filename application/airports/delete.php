<?php
require_once '../db.php';

$code = $_GET['code'] ?? '';

if ($code) {
    try {
        $stmt = $pdo->prepare("DELETE FROM airport WHERE airport_code = ?");
        $stmt->execute([$code]);
    } catch (PDOException $e) {
        // Satisfies safety requirements for deleting referenced records
        die("<script>alert('Cannot delete this airport because it is referenced by flight schedules or routes.'); window.location.href='index.php';</script>");
    }
}

header("Location: index.php");
exit;
?>