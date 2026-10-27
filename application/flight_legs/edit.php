<?php
require_once '../db.php';

$message = "";
$id = $_GET['id'] ?? '';

if (!$id) {
    header("Location: index.php");
    exit;
}

// Fetch existing record
$stmt = $pdo->prepare("SELECT * FROM flight_leg WHERE leg_id = ?");
$stmt->execute([$id]);
$leg = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$leg) {
    header("Location: index.php");
    exit;
}

// Fetch available options
$schedules = $pdo->query("SELECT * FROM flight_schedule")->fetchAll(PDO::FETCH_ASSOC);
$airports = $pdo->query("SELECT * FROM airport")->fetchAll(PDO::FETCH_ASSOC);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $flight_number = $_POST['flight_number'] ?? '';
    $actual_departure_datetime = $_POST['actual_departure_datetime'] ?? '';
    $actual_arrival_datetime = $_POST['actual_arrival_datetime'] ?? '';
    $origin_airport_code = $_POST['origin_airport_code'] ?? '';
    $destination_airport_code = $_POST['destination_airport_code'] ?? '';

    if (!empty($flight_number) && !empty($actual_departure_datetime) && !empty($origin_airport_code) && !empty($destination_airport_code)) {
        try {
            $updateStmt = $pdo->prepare("UPDATE flight_leg SET flight_number = ?, actual_departure_datetime = ?, actual_arrival_datetime = ?, origin_airport_code = ?, destination_airport_code = ? WHERE leg_id = ?");
            $updateStmt->execute([$flight_number, $actual_departure_datetime, $actual_arrival_datetime, $origin_airport_code, $destination_airport_code, $id]);
            header("Location: index.php");
            exit;
        } catch (PDOException $e) {
            $message = "Error: " . $e->getMessage();
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
    <title>Edit Flight Leg - HarborWing Air</title>
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
    </style>
</head>
<body>

    <h1>Edit Flight Leg ID: <?php echo htmlspecialchars($id); ?></h1>
    <p><a href="index.php">← Back to Flight Legs</a></p>

    <?php if ($message): ?>
        <p class="error"><?php echo htmlspecialchars($message); ?></p>
    <?php endif; ?>

    <div class="card">
        <form method="POST">
            <div class="form-group">
                <label>Flight Number:</label>
                <select name="flight_number" required>
                    <option value="">Select Flight Number</option>
                    <?php foreach ($schedules as $sched): ?>
                        <option value="<?php echo htmlspecialchars($sched['flight_number']); ?>" <?php echo ($leg['flight_number'] == $sched['flight_number']) ? 'selected' : ''; ?>>
                            Flight <?php echo htmlspecialchars($sched['flight_number']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group">
                <label>Actual Departure Datetime:</label>
                <input type="datetime-local" name="actual_departure_datetime" value="<?php echo htmlspecialchars(date('Y-m-d\TH:i', strtotime($leg['actual_departure_datetime']))); ?>" required>
            </div>

            <div class="form-group">
                <label>Actual Arrival Datetime:</label>
                <input type="datetime-local" name="actual_arrival_datetime" value="<?php echo htmlspecialchars($leg['actual_arrival_datetime'] ? date('Y-m-d\TH:i', strtotime($leg['actual_arrival_datetime'])) : ''); ?>">
            </div>

            <div class="form-group">
                <label>Origin Airport Code:</label>
                <select name="origin_airport_code" required>
                    <option value="">Select Airport</option>
                    <?php foreach ($airports as $apt): ?>
                        <option value="<?php echo htmlspecialchars($apt['airport_code']); ?>" <?php echo ($leg['origin_airport_code'] == $apt['airport_code']) ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($apt['airport_code']); ?> - <?php echo htmlspecialchars($apt['airport_name'] ?? ''); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group">
                <label>Destination Airport Code:</label>
                <select name="destination_airport_code" required>
                    <option value="">Select Airport</option>
                    <?php foreach ($airports as $apt): ?>
                        <option value="<?php echo htmlspecialchars($apt['airport_code']); ?>" <?php echo ($leg['destination_airport_code'] == $apt['airport_code']) ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($apt['airport_code']); ?> - <?php echo htmlspecialchars($apt['airport_name'] ?? ''); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <button type="submit" class="btn">Update Flight Leg</button>
        </form>
    </div>

</body>
</html>