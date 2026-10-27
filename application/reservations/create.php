<?php
require_once '../db.php';

$message = "";

// Auto-seed lookup tables if they are empty to satisfy foreign key constraints
try {
    $pdo->exec("INSERT IGNORE INTO status_code (status_code, description) VALUES ('CONFIRMED', 'Confirmed'), ('PENDING', 'Pending'), ('CANCELLED', 'Cancelled')");
    $pdo->exec("INSERT IGNORE INTO ticket_type (ticket_type_code, description) VALUES ('ET', 'Electronic'), ('PT', 'Paper')");
    $pdo->exec("INSERT IGNORE INTO travel_class (travel_class_code, description) VALUES ('Y', 'Economy'), ('C', 'Business'), ('F', 'First')");
} catch (PDOException $e) {
    // Ignore if tables aren't set up yet
}

// Fetch lookup data from database
$passengers = $pdo->query("SELECT passenger_id, first_name, last_name FROM passenger")->fetchAll(PDO::FETCH_ASSOC);
$agents = $pdo->query("SELECT agent_id, agent_name FROM booking_agent")->fetchAll(PDO::FETCH_ASSOC);
$statuses = $pdo->query("SELECT status_code, description FROM status_code")->fetchAll(PDO::FETCH_ASSOC);
$ticket_types = $pdo->query("SELECT ticket_type_code, description FROM ticket_type")->fetchAll(PDO::FETCH_ASSOC);
$travel_classes = $pdo->query("SELECT travel_class_code, description FROM travel_class")->fetchAll(PDO::FETCH_ASSOC);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $passenger_id = trim($_POST['passenger_id'] ?? '');
    $agent_id = trim($_POST['agent_id'] ?? '');
    $status_code = trim($_POST['reservation_status_code'] ?? '');
    $ticket_code = trim($_POST['ticket_type_code'] ?? '');
    $travel_code = trim($_POST['travel_class_code'] ?? '');
    $number_in_party = trim($_POST['number_in_party'] ?? 1);
    $creation_datetime = date('Y-m-d H:i:s');

    if (!empty($passenger_id) && !empty($status_code) && !empty($ticket_code) && !empty($travel_code)) {
        try {
            $pdo->beginTransaction();

            $stmt = $pdo->prepare("INSERT INTO reservation (passenger_id, agent_id, reservation_status_code, ticket_type_code, travel_class_code, creation_datetime, number_in_party) VALUES (?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([
                $passenger_id, 
                $agent_id ?: null, 
                $status_code, 
                $ticket_code, 
                $travel_code, 
                $creation_datetime, 
                $number_in_party
            ]);
            
            $pdo->commit();
            header("Location: index.php");
            exit;
        } catch (PDOException $e) {
            $pdo->rollBack();
            $message = "Error: " . $e->getMessage();
        }
    } else {
        $message = "Passenger, Status, Ticket Type, and Travel Class are required fields.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Create Reservation - HarborWing Air</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 40px; background: #f4f7f6; }
        form { background: white; padding: 25px; border-radius: 5px; width: 450px; box-shadow: 0 2px 5px rgba(0,0,0,0.1); }
        input, select, button { width: 100%; padding: 10px; margin-top: 10px; box-sizing: border-box; }
        button { background: #004080; color: white; border: none; cursor: pointer; margin-top: 15px; }
        button:hover { background: #003366; }
        p { color: red; font-weight: bold; }
        label { font-weight: bold; font-size: 14px; color: #333; margin-top: 10px; display: block; }
    </style>
</head>
<body>
    <h2>Create New Reservation</h2>
    <?php if($message): ?><p><?php echo htmlspecialchars($message); ?></p><?php endif; ?>
    
    <form method="POST">
        <label>Passenger:</label>
        <select name="passenger_id" required>
            <option value="">Select Passenger</option>
            <?php foreach ($passengers as $p): ?>
                <option value="<?php echo $p['passenger_id']; ?>"><?php echo htmlspecialchars($p['first_name'] . ' ' . $p['last_name']); ?></option>
            <?php endforeach; ?>
        </select>
        
        <label>Booking Agent (Optional):</label>
        <select name="agent_id">
            <option value="">Direct Booking / None</option>
            <?php foreach ($agents as $a): ?>
                <option value="<?php echo $a['agent_id']; ?>"><?php echo htmlspecialchars($a['agent_name'] ?? 'Agent #' . $a['agent_id']); ?></option>
            <?php endforeach; ?>
        </select>

        <label>Reservation Status Code:</label>
        <select name="reservation_status_code" required>
            <option value="">Select Status</option>
            <?php foreach ($statuses as $st): ?>
                <option value="<?php echo $st['status_code']; ?>"><?php echo htmlspecialchars($st['status_code'] . ' - ' . $st['description']); ?></option>
            <?php endforeach; ?>
        </select>

        <label>Ticket Type Code:</label>
        <select name="ticket_type_code" required>
            <option value="">Select Ticket Type</option>
            <?php foreach ($ticket_types as $tt): ?>
                <option value="<?php echo $tt['ticket_type_code']; ?>"><?php echo htmlspecialchars($tt['ticket_type_code'] . ' - ' . $tt['description']); ?></option>
            <?php endforeach; ?>
        </select>

        <label>Travel Class Code:</label>
        <select name="travel_class_code" required>
            <option value="">Select Travel Class</option>
            <?php foreach ($travel_classes as $tc): ?>
                <option value="<?php echo $tc['travel_class_code']; ?>"><?php echo htmlspecialchars($tc['travel_class_code'] . ' - ' . $tc['description']); ?></option>
            <?php endforeach; ?>
        </select>

        <label>Number in Party:</label>
        <input type="number" name="number_in_party" value="1" min="1" required>
        
        <button type="submit">Save Reservation Form</button>
    </form>
    
    <p><a href="index.php">← Back to Reservations</a></p>
</body>
</html>