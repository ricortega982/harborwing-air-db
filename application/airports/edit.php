<?php
require_once '../db.php';

$message = "";
$code = $_GET['code'] ?? '';

if (!$code) {
    header("Location: index.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['airport_name'] ?? '');
    $location = trim($_POST['location'] ?? '');
    $details = trim($_POST['other_details'] ?? '');

    if (!empty($name) && !empty($location)) {
        try {
            $stmt = $pdo->prepare("UPDATE airport SET airport_name = ?, location = ?, other_details = ? WHERE airport_code = ?");
            $stmt->execute([$name, $location, $details ?: null, $code]);
            header("Location: index.php");
            exit;
        } catch (PDOException $e) {
            $message = "Error: " . $e->getMessage();
        }
    } else {
        $message = "Airport name and location are required.";
    }
}

$stmt = $pdo->prepare("SELECT * FROM airport WHERE airport_code = ?");
$stmt->execute([$code]);
$airport = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$airport) {
    die("Airport not found.");
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Airport</title>
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
    <h2>Edit Airport: <?php echo htmlspecialchars($airport['airport_code']); ?></h2>
    <?php if($message): ?><p><?php echo htmlspecialchars($message); ?></p><?php endif; ?>
    
    <form method="POST">
        <label>Airport Code (Locked):</label>
        <input type="text" value="<?php echo htmlspecialchars($airport['airport_code']); ?>" disabled>
        
        <label>Airport Name:</label>
        <input type="text" name="airport_name" value="<?php echo htmlspecialchars($airport['airport_name']); ?>" required>

        <label>Location:</label>
        <input type="text" name="location" value="<?php echo htmlspecialchars($airport['location']); ?>" required>

        <label>Other Details:</label>
        <textarea name="other_details"><?php echo htmlspecialchars($airport['other_details'] ?? ''); ?></textarea>
        
        <button type="submit">Update Airport</button>
    </form>
    
    <p><a href="index.php">← Back to Airports</a></p>
</body>
</html>