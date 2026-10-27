<?php
require_once 'db.php';

$code = $_GET['code'] ?? '';

if ($code) {
    try {
        $stmt = $pdo->prepare("DELETE FROM ticket_type WHERE ticket_type_code = ?");
        $stmt->execute([$code]);
    } catch (PDOException $e) {
        die("Delete failed: " . $e->getMessage());
    }
}

header("Location: index.php");
exit;
?>