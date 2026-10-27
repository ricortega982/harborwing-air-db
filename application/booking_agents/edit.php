<?php
require_once '../db.php';

$message = "";
$id = $_GET['id'] ?? '';

if (!$id) {
    header("Location: index.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['agent_name'] ?? '');
    $details = trim($_POST['additional_details'] ?? '');

    if (!empty($name)) {
        try {
            $stmt = $pdo->prepare("UPDATE booking_agent SET agent_name = ?, additional_details = ? WHERE agent_id = ?");
            $stmt->execute([$name, $details ?: null, $id]);
            header("Location: index.php");
            exit;
        } catch (PDOException $e) {
            $message = "Error: " . $e->getMessage();
        }
    } else {
        $message = "Agent name is required.";
    }
}

$stmt = $pdo->prepare("SELECT * FROM booking_agent WHERE agent_id = ?");
$stmt->execute([$id]);
$agent = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$agent) {
    die("Booking agent not found.");
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Booking Agent</title>
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
    <h2>Edit Booking Agent ID: <?php echo htmlspecialchars($agent['agent_id']); ?></h2>
    <?php if($message): ?><p><?php echo htmlspecialchars($message); ?></p><?php endif; ?>
    
    <form method="POST">
        <label>Agent Name / Agency:</label>
        <input type="text" name="agent_name" value="<?php echo htmlspecialchars($agent['agent_name'] ?? ''); ?>" required>

        <label>Additional Details:</label>
        <textarea name="additional_details" rows="3"><?php echo htmlspecialchars($agent['additional_details'] ?? ''); ?></textarea>
        
        <button type="submit">Update Booking Agent</button>
    </form>
    
    <p><a href="index.php">← Back to Booking Agents</a></p>
</body>
</html>