<?php
require_once '../db.php';

$message = "";
$fn = $_GET['flight_number'] ?? '';
$ac = $_GET['aircraft_type_code'] ?? '';
$from = $_GET['valid_from_date'] ?? '';
$to = $_GET['valid_to_date'] ?? '';

if (!$fn || !$ac || !$from || !$to) {
    header("Location: index.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $cost_amount = trim($_POST['cost_amount'] ?? '');

    if (!empty($cost_amount)) {
        try {
            $stmt = $pdo->prepare("UPDATE flight_cost SET cost_amount = ? WHERE flight_number = ? AND aircraft_type_code = ? AND valid_from_date = ? AND valid_to_date = ?");
            $stmt->execute([$cost_amount, $fn, $ac, $from, $to]);
            header("Location: index.php");
            exit;
        } catch (PDOException $e) {
            $message = "Error: " . $e->getMessage();
        }
    } else {
        $message = "Cost amount is required.";
    }
}

$stmt = $pdo->prepare("SELECT * FROM flight_cost WHERE flight_number = ? AND aircraft_type_code = ? AND valid_from_date = ? AND valid_to_date = ?");
$stmt->execute([$fn, $ac, $from, $to]);
$fc = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$fc) {
    die("Flight cost record not found.");
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Flight Cost</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 40px; background: #f4f7f6; }
        form { background: white; padding: 20px; border-radius: 5px; width: 400px; box-shadow: 0 2px 5px rgba(0,0,0,0.1); }
        input, button { width: 100%; padding: 10px; margin-top: 10px; box-sizing: border-box; }
        button { background: #004080; color: white; border: none; cursor: pointer; margin-top: 15px; }
        button:hover { background: #003366; }
        p { color: red; font-weight: bold; }
        label { font-weight: bold; font-size: 14px; color: #333; margin-top: 10px; display: block; }
    </style>
</head>
<body>
    <h2>Edit Flight Cost</h2>
    <?php if($message): ?><p><?php echo htmlspecialchars($message); ?></p><?php endif; ?>
    
    <form method="POST">
        <label>Flight Number (Locked):</label>
        <input type="text" value="<?php echo htmlspecialchars($fc['flight_number']); ?>" disabled>

        <label>Aircraft Type Code (Locked):</label>
        <input type="text" value="<?php echo htmlspecialchars($fc['aircraft_type_code']); ?>" disabled>

        <label>Valid From Date (Locked):</label>
        <input type="text" value="<?php echo htmlspecialchars($fc['valid_from_date']); ?>" disabled>

        <label>Valid To Date (Locked):</label>
        <input type="text" value="<?php echo htmlspecialchars($fc['valid_to_date']); ?>" disabled>

        <label>Cost Amount ($):</label>
        <input type="number" step="0.01" name="cost_amount" value="<?php echo htmlspecialchars($fc['cost_amount']); ?>" required>
        
        <button type="submit">Update Flight Cost</button>
    </form>
    
    <p><a href="index.php">← Back to Flight Costs</a></p>
</body>
</html>