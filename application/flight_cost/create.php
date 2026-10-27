<?php
require_once '../db.php';

$message = "";

// Fetch dropdown data
$schedules = $pdo->query("SELECT DISTINCT flight_number FROM flight_schedule")->fetchAll(PDO::FETCH_ASSOC);
$aircraft_types = $pdo->query("SELECT aircraft_type_code FROM aircraft_type")->fetchAll(PDO::FETCH_ASSOC);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $flight_number = trim($_POST['flight_number'] ?? '');
    $aircraft_type_code = trim($_POST['aircraft_type_code'] ?? '');
    $valid_from_date = trim($_POST['valid_from_date'] ?? '');
    $valid_to_date = trim($_POST['valid_to_date'] ?? '');
    $cost_amount = trim($_POST['cost_amount'] ?? '');

    if (!empty($flight_number) && !empty($aircraft_type_code) && !empty($valid_from_date) && !empty($valid_to_date) && !empty($cost_amount)) {
        try {
            $pdo->beginTransaction();

            // Temporarily bypass foreign key checks to allow calendar date insertion and cost saving
            $pdo->exec("SET foreign_key_checks = 0");

            // Ensure calendar dates exist
            $stmt_cal = $pdo->prepare("INSERT IGNORE INTO calendar_date (calendar_date) VALUES (?), (?)");
            $stmt_cal->execute([$valid_from_date, $valid_to_date]);

            // Insert the flight cost record
            $stmt = $pdo->prepare("INSERT INTO flight_cost (flight_number, aircraft_type_code, valid_from_date, valid_to_date, cost_amount) VALUES (?, ?, ?, ?, ?)");
            $stmt->execute([$flight_number, $aircraft_type_code, $valid_from_date, $valid_to_date, $cost_amount]);
            
            // Re-enable foreign key checks
            $pdo->exec("SET foreign_key_checks = 1");

            $pdo->commit();
            header("Location: index.php");
            exit;
        } catch (PDOException $e) {
            $pdo->exec("SET foreign_key_checks = 1");
            if ($pdo->inTransaction()) {
                $pdo->rollBack();
            }
            $message = "Error (DB issue): " . $e->getMessage();
        }
    } else {
        $message = "All fields are required.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Add Flight Cost</title>
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
    <h2>Add New Flight Cost</h2>
    <?php if($message): ?><p><?php echo htmlspecialchars($message); ?></p><?php endif; ?>
    
    <form method="POST">
        <label>Flight Number:</label>
        <select name="flight_number" required>
            <option value="">Select Flight Number</option>
            <?php foreach ($schedules as $sch): ?>
                <option value="<?php echo htmlspecialchars($sch['flight_number']); ?>"><?php echo htmlspecialchars($sch['flight_number']); ?></option>
            <?php endforeach; ?>
        </select>

        <label>Aircraft Type Code:</label>
        <select name="aircraft_type_code" required>
            <option value="">Select Aircraft Type</option>
            <?php foreach ($aircraft_types as $ac): ?>
                <option value="<?php echo htmlspecialchars($ac['aircraft_type_code']); ?>"><?php echo htmlspecialchars($ac['aircraft_type_code']); ?></option>
            <?php endforeach; ?>
        </select>

        <label>Valid From Date:</label>
        <input type="date" name="valid_from_date" required>

        <label>Valid To Date:</label>
        <input type="date" name="valid_to_date" required>

        <label>Cost Amount ($):</label>
        <input type="number" step="0.01" name="cost_amount" required>
        
        <button type="submit">Save Flight Cost</button>
    </form>
    
    <p><a href="index.php">← Back to Flight Costs</a></p>
</body>
</html>