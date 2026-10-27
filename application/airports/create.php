<?php
require_once '../db.php';

$message = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $code = trim($_POST['airport_code'] ?? '');
    $name = trim($_POST['airport_name'] ?? '');
    $location = trim($_POST['location'] ?? '');
    $details = trim($_POST['other_details'] ?? '');

    if (!empty($code) && !empty($name) && !empty($location)) {
        try {
            $stmt = $pdo->prepare("INSERT INTO airport (airport_code, airport_name, location, other_details) VALUES (?, ?, ?, ?)");
            $stmt->execute([$code, $name, $location, $details ?: null]);
            header("Location: index.php");
            exit;
        } catch (PDOException $e) {
            $message = "Error (Duplicate or DB issue): " . $e->getMessage();
        }
    } else {
        $message = "Please fill in all required fields.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Add Airport</title>
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
    <h2>Add New Airport</h2>
    <?php if($message): ?><p><?php echo htmlspecialchars($message); ?></p><?php endif; ?>
    
    <form method="POST">
        <label>Airport Code (e.g., PPS):</label>
        <input type="text" name="airport_code" required maxlength="10">
        
        <label>Airport Name:</label>
        <input type="text" name="airport_name" required>

        <label>Location:</label>
        <input type="text" name="location" required>

        <label>Other Details:</label>
        <textarea name="other_details"></textarea>
        
        <button type="submit">Save Airport</button>
    </form>
    
    <p><a href="index.php">← Back to Airports</a></p>
</body>
</html>