<?php
require_once '../db.php';

$message = "";

// Handle form submission to create a flight leg
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $flight_number = $_POST['flight_number'] ?? '';
    $actual_departure_datetime = $_POST['actual_departure_datetime'] ?? '';
    $actual_arrival_datetime = $_POST['actual_arrival_datetime'] ?? '';
    $origin_airport_code = $_POST['origin_airport_code'] ?? '';
    $destination_airport_code = $_POST['destination_airport_code'] ?? '';

    if (!empty($flight_number) && !empty($actual_departure_datetime) && !empty($origin_airport_code) && !empty($destination_airport_code)) {
        try {
            $stmt = $pdo->prepare("INSERT INTO flight_leg (flight_number, actual_departure_datetime, actual_arrival_datetime, origin_airport_code, destination_airport_code) VALUES (?, ?, ?, ?, ?)");
            $stmt->execute([$flight_number, $actual_departure_datetime, $actual_arrival_datetime, $origin_airport_code, $destination_airport_code]);
            header("Location: index.php");
            exit;
        } catch (PDOException $e) {
            $message = "Error: " . $e->getMessage();
        }
    } else {
        $message = "Please fill in all required fields.";
    }
}

// Fetch available schedules/flight numbers and airports from the database based on the EER schema
$schedules = $pdo->query("SELECT * FROM flight_schedule")->fetchAll(PDO::FETCH_ASSOC);
$airports = $pdo->query("SELECT * FROM airport")->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Add New Flight Leg - HarborWing Air</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 40px; background: #f4f7f6; }
        h1 { color: #004080; }
        .card { background: white; padding: 20px; border-radius: 5px; max-width: 500px; box-shadow: 0 2px 5px rgba(0,0,0,0.1); }
        .form-group { margin-bottom: 15px; }
        label { display: block; margin-bottom: 5px; font-weight: bold; color: #333; }
        select, input { width: 100%; padding: 8px; box-sizing: border-box; border: 1px solid #ccc; border-radius: 4px; }
        .btn { padding: 10px 15px; background: #004080; color: white; border: none; border-radius: 4px; cursor: pointer; }
        .btn:hover { background: #003366; }
        .error { color: red; font-weight: bold; margin-bottom: 15px; }
        .warning { color: #b8860b; font-size: 0.9em; margin-top: 5px; }
    </style>
</head>
<body>

    <h1>Add New Flight Leg</h1>
    <p><a href="index.php">← Back to Flight Legs</a></p>

    <?php if ($message): ?>
        <p class="error"><?php echo htmlspecialchars($message); ?></p>
    <?php endif; ?>

    <div class="card">
        <form method="POST">
            <div class="form-group">
                <label>Flight Number / Schedule:</label>
                <?php if (count($schedules) > 0): ?>
                    <select name="flight_number" required>
                        <option value="">Select Flight Number</option>
                        <?php foreach ($schedules as $sched): ?>
                            <option value="<?php echo htmlspecialchars($sched['flight_number']); ?>">
                                Flight <?php echo htmlspecialchars($sched['flight_number']); ?> 
                                (<?php echo htmlspecialchars($sched['departure_airport_code'] ?? ''); ?> → <?php echo htmlspecialchars($sched['arrival_airport_code'] ?? ''); ?>)
                            </option>
                        <?php endforeach; ?>
                    </select>
                <?php else: ?>
                    <p class="warning">⚠️ No flight schedules found. Please add records in <a href="../flight_schedules/index.php">Flight Schedules</a> first!</p>
                <?php endif; ?>
            </div>

            <div class="form-group">
                <label>Actual Departure Datetime:</label>
                <input type="datetime-local" name="actual_departure_datetime" required>
            </div>

            <div class="form-group">
                <label>Actual Arrival Datetime:</label>
                <input type="datetime-local" name="actual_arrival_datetime">
            </div>

            <div class="form-group">
                <label>Origin Airport Code:</label>
                <?php if (count($airports) > 0): ?>
                    <select name="origin_airport_code" required>
                        <option value="">Select Airport</option>
                        <?php foreach ($airports as $apt): ?>
                            <option value="<?php echo htmlspecialchars($apt['airport_code']); ?>">
                                <?php echo htmlspecialchars($apt['airport_code']); ?> - <?php echo htmlspecialchars($apt['airport_name'] ?? ''); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                <?php else: ?>
                    <p class="warning">⚠️ No airports found. Please add records in <a href="../airports/index.php">Airports</a> first!</p>
                <?php endif; ?>
            </div>

            <div class="form-group">
                <label>Destination Airport Code:</label>
                <?php if (count($airports) > 0): ?>
                    <select name="destination_airport_code" required>
                        <option value="">Select Airport</option>
                        <?php foreach ($airports as $apt): ?>
                            <option value="<?php echo htmlspecialchars($apt['airport_code']); ?>">
                                <?php echo htmlspecialchars($apt['airport_code']); ?> - <?php echo htmlspecialchars($apt['airport_name'] ?? ''); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                <?php else: ?>
                    <p class="warning">⚠️ No airports found.</p>
                <?php endif; ?>
            </div>

            <button type="submit" class="btn">Save Flight Leg</button>
        </form>
    </div>

</body>
</html>