<?php
require_once '../db.php';

$message = "";

// Fetch statuses for dropdown
$statuses = $pdo->query("SELECT status_code, description FROM status_code")->fetchAll(PDO::FETCH_ASSOC);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $payment_amount = trim($_POST['payment_amount'] ?? '');
    $payment_date = trim($_POST['payment_date'] ?? '');
    $payment_status_code = trim($_POST['payment_status_code'] ?? '');

    if (!empty($payment_amount) && !empty($payment_status_code)) {
        try {
            $stmt = $pdo->prepare("INSERT INTO payment (payment_amount, payment_date, payment_status_code) VALUES (?, ?, ?)");
            $stmt->execute([
                $payment_amount, 
                $payment_date ? $payment_date . ' ' . date('H:i:s') : date('Y-m-d H:i:s'), 
                $payment_status_code
            ]);
            header("Location: index.php");
            exit;
        } catch (PDOException $e) {
            $message = "Error: " . $e->getMessage();
        }
    } else {
        $message = "Payment Amount and Status Code are required fields.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Add Payment - HarborWing Air</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 40px; background: #f4f7f6; }
        form { background: white; padding: 20px; border-radius: 5px; width: 400px; box-shadow: 0 2px 5px rgba(0,0,0,0.1); }
        input, select, button { width: 100%; padding: 10px; margin-top: 10px; box-sizing: border-box; }
        button { background: #004080; color: white; border: none; cursor: pointer; margin-top: 15px; }
        button:hover { background: #003366; }
        p { color: red; font-weight: bold; }
        label { font-weight: bold; font-size: 14px; color: #333; margin-top: 10px; display: block; }
    </style>
</head>
<body>
    <h2>Add New Payment</h2>
    <?php if($message): ?><p><?php echo htmlspecialchars($message); ?></p><?php endif; ?>
    
    <form method="POST">
        <label>Payment Amount ($):</label>
        <input type="number" step="0.01" name="payment_amount" required>

        <label>Payment Status Code:</label>
        <select name="payment_status_code" required>
            <option value="">Select Status</option>
            <?php foreach ($statuses as $st): ?>
                <option value="<?php echo htmlspecialchars($st['status_code']); ?>"><?php echo htmlspecialchars($st['status_code'] . ' - ' . $st['description']); ?></option>
            <?php endforeach; ?>
        </select>

        <label>Payment Date:</label>
        <input type="date" name="payment_date" value="<?php echo date('Y-m-d'); ?>">
        
        <button type="submit">Save Payment</button>
    </form>
    
    <p><a href="index.php">← Back to Payments</a></p>
</body>
</html>