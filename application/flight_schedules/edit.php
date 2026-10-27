<?php
require_once '../db.php';

$message = "";
$flight_number = $_GET['flight_number'] ?? '';

if (!$flight_number) {
    header("Location: index.php");
    exit;
}

// Fetch dropdown data
$airports = $pdo->query("SELECT airport_code, airport_name FROM airport")->fetchAll(PDO::FETCH_ASSOC);
$airlines = $pdo->query("SELECT airline_code FROM airline")->fetchAll(PDO::FETCH_ASSOC);
$aircraft_types = $pdo->query("SELECT aircraft_type_code, model_name FROM aircraft_type")->fetchAll(PDO::FETCH_ASSOC);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $airline_code = trim($_POST['airline_code'] ?? '');
    $usual_aircraft_type_code = trim($_POST['usual_aircraft_type_code'] ?? '');
    $origin_airport = trim($_POST['origin_airport_code'] ?? '');
    $destination_airport = trim($_POST['destination_airport_code'] ?? '');
    $dep_time = trim($_POST['scheduled_departure_datetime'] ?? '');
    $arr_time = trim($_POST['scheduled_arrival_datetime'] ?? '');

    if (!empty($origin_airport) && !empty($destination_airport)) {
        try {
            $stmt = $pdo->prepare("UPDATE flight_schedule SET airline_code = ?, usual_aircraft_type_code = ?, origin_airport_code = ?, destination_airport_code = ?, scheduled_departure_datetime = ?, scheduled_arrival_datetime = ? WHERE flight_number = ?");
            $stmt->execute([$airline_code ?: null, $usual_aircraft_type_code ?: null, $origin_airport, $destination_airport, $dep_time ?: null, $arr_time ?: null, $flight_number]);
            header("Location: index.php");
            exit;
        } catch (PDOException $e) {
            $message = "Error: " . $e->getMessage();
        }
    } else {
        $message = "Airport fields are required.";
    }
}

$stmt = $pdo->prepare("SELECT * FROM flight_schedule WHERE flight_number = ?");
$stmt->execute([$flight_number]);
$schedule = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$schedule) {
    die("Flight schedule not found.");
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Flight Schedule</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 40px; background: #f4f7f6; }
        form { background: white; padding: 20px; border-radius: 5px; width: 450px; box-shadow: 0 2px 5px rgba(0,0,0,0.1); }
        input, select, button { width: 100%; padding: 10px; margin-top: 10px; box-sizing: border-box; }
        button { background: #004080; color: white; border: none; cursor: pointer; }
        button:hover { background: #003366; }
        p { color: red; font-weight: bold; }
    </style>
</head>
<body>
    <h2>Edit Flight: <?php echo htmlspecialchars($schedule['flight_number']); ?></h2>
    <?php if($message): ?><p><?php echo htmlspecialchars($message); ?></p><?php endif; ?>
    
    <form method="POST">
        <label>Flight Number (Locked):</label>
        <input type="text" value="<?php echo htmlspecialchars($schedule['flight_number']); ?>" disabled>

        <label>Airline Code:</label>
        <select name="airline_code">
            <option value="">Select Airline</option>
            <?php foreach ($airlines as $airline): ?>
                <option value="<?php echo htmlspecialchars($airline['airline_code']); ?>" <?php echo ($schedule['airline_code'] == $airline['airline_code']) ? 'selected' : ''; ?>>
                    <?php echo htmlspecialchars($airline['airline_code']); ?>
                </option>
            <?php endforeach; ?>
        </select>

        <label>Aircraft Type Code:</label>
        <select name="usual_aircraft_type_code">
            <option value="">Select Aircraft Type</option>
            <?php foreach ($aircraft_types as $ac): ?>
                <option value="<?php echo htmlspecialchars($ac['aircraft_type_code']); ?>" <?php echo ($schedule['usual_aircraft_type_code'] == $ac['aircraft_type_code']) ? 'selected' : ''; ?>>
                    <?php echo htmlspecialchars($ac['aircraft_type_code'] . (!empty($ac['model_name']) ? ' - ' . $ac['model_name'] : '')); ?>
                </option>
            <?php endforeach; ?>
        </select>
        
        <label>Origin Airport:</label>
        <select name="origin_airport_code" required>
            <?php foreach ($airports as $apt): ?>
                <option value="<?php echo htmlspecialchars($apt['airport_code']); ?>" <?php echo ($schedule['origin_airport_code'] == $apt['airport_code']) ? 'selected' : ''; ?>>
                    <?php echo htmlspecialchars($apt['airport_code'] . ' - ' . $apt['airport_name']); ?>
                </option>
            <?php endforeach; ?>
        </select>

        <label>Destination Airport:</label>
        <select name="destination_airport_code" required>
            <?php foreach ($airports as $apt): ?>
                <option value="<?php echo htmlspecialchars($apt['airport_code']); ?>" <?php echo ($schedule['destination_airport_code'] == $apt['airport_code']) ? 'selected' : ''; ?>>
                    <?php echo htmlspecialchars($apt['airport_code'] . ' - ' . $apt['airport_name']); ?>
                </option>
            <?php endforeach; ?>
        </select>

        <label>Scheduled Departure Datetime:</label>
        <input type="datetime-local" name="scheduled_departure_datetime" value="<?php echo htmlspecialchars($schedule['scheduled_departure_datetime'] ? date('Y-m-d\TH:i', strtotime($schedule['scheduled_departure_datetime'])) : ''); ?>">

        <label>Scheduled Arrival Datetime:</label>
        <input type="datetime-local" name="scheduled_arrival_datetime" value="<?php echo htmlspecialchars($schedule['scheduled_arrival_datetime'] ? date('Y-m-d\TH:i', strtotime($schedule['scheduled_arrival_datetime'])) : ''); ?>">
        
        <button type="submit">Update Flight Schedule</button>
    </form>
    
    <p><a href="index.php">← Back to Flight Schedules</a></p>
</body>
</html>