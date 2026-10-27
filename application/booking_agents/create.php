<?php
require_once '../db.php';

$message = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['agent_name'] ?? '');
    $details = trim($_POST['additional_details'] ?? '');

    if (!empty($name)) {
        try {
            $stmt = $pdo->prepare("INSERT INTO booking_agent (agent_name, additional_details) VALUES (?, ?)");
            $stmt->execute([$name, $details ?: null]);
            header("Location: index.php");
            exit;
        } catch (PDOException $e) {
            $message = "Error: " . $e->getMessage();
        }
    } else {
        $message = "Agent name is required.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Add Booking Agent</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 40px; background: #f4f7f6; }
        form { background: white; padding: 20px; border-radius: 5px; width: 400px; box-shadow: 0 2px 5px rgba(0,0,0,0.1); }
        input, textarea, button { width: 100%; padding: 10px; margin-top: 10px; box-sizing: border-box; }
        button { background: #004080; color: white; border: none; cursor: pointer; }
        button:hover { background: #003366; }
        p { color: red; font-weight: bold; }
    </style>
</head>
<body>
    <h2>Add New Booking Agent</h2>
    <?php if($message): ?><p><?php echo htmlspecialchars($message); ?></p><?php endif; ?>
    
    <form method="POST">
        <label>Agent Name / Agency:</label>
        <input type="text" name="agent_name" required>

        <label>Additional Details:</label>
        <textarea name="additional_details" rows="3"></textarea>
        
        <button type="submit">Save Booking Agent</button>
    </form>
    
    <p><a href="index.php">← Back to Booking Agents</a></p>
</body>
</html>