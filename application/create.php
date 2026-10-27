<?php
require_once 'db.php';

$message = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $code = trim($_POST['ticket_type_code']);
    $desc = trim($_POST['description']);

    if (!empty($code) && !empty($desc)) {
        try {
            $stmt = $pdo->prepare("INSERT INTO ticket_type (ticket_type_code, description) VALUES (?, ?)");
            $stmt->execute([$code, $desc]);
            $message = "New ticket type added successfully!";
        } catch (PDOException $e) {
            $message = "Error: " . $e->getMessage();
        }
    } else {
        $message = "Please fill in all fields.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Add Ticket Type</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 40px; background: #f4f7f6; }
        form { background: white; padding: 20px; border-radius: 5px; width: 300px; box-shadow: 0 2px 5px rgba(0,0,0,0.1); }
        input, button { width: 100%; padding: 10px; margin-top: 10px; box-sizing: border-box; }
        button { background: #004080; color: white; border: none; cursor: pointer; }
        button:hover { background: #003366; }
        p { color: green; font-weight: bold; }
    </style>
</head>
<body>
    <h2>Add New Ticket Type</h2>
    <?php if($message): ?><p><?php echo htmlspecialchars($message); ?></p><?php endif; ?>
    
    <form method="POST">
        <label>Ticket Type Code:</label>
        <input type="text" name="ticket_type_code" required>
        
        <label>Description:</label>
        <input type="text" name="description" required>
        
        <button type="submit">Save Ticket Type</button>
    </form>
    
    <p><a href="index.php">← Back to Dashboard</a></p>
</body>
</html>