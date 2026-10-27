<?php
require_once '../db.php';

$reservation_id = $_GET['id'] ?? '';
if (!$reservation_id) {
    header("Location: index.php");
    exit;
}

$message = "";

// Handle adding a flight leg link or payment link using exact database table names
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['add_leg'])) {
        $leg_id = $_POST['leg_id'];
        if (!empty($leg_id)) {
            try {
                $stmt = $pdo->prepare("INSERT INTO reservation_leg_link (reservation_id, leg_id) VALUES (?, ?)");
                $stmt->execute([$reservation_id, $leg_id]);
                header("Location: view.php?id=" . $reservation_id);
                exit;
            } catch (PDOException $e) {
                $message = "Error: This flight leg is already linked or invalid.";
            }
        }
    } elseif (isset($_POST['add_payment'])) {
        $payment_id = $_POST['payment_id'];
        if (!empty($payment_id)) {
            try {
                $stmt = $pdo->prepare("INSERT INTO reservation_payment_link (reservation_id, payment_id) VALUES (?, ?)");
                $stmt->execute([$reservation_id, $payment_id]);
                header("Location: view.php?id=" . $reservation_id);
                exit;
            } catch (PDOException $e) {
                $message = "Error: This payment is already linked or invalid.";
            }
        }
    }
}

// Handle unlinking
if (isset($_GET['unlink_leg'])) {
    $leg_id = $_GET['unlink_leg'];
    $stmt = $pdo->prepare("DELETE FROM reservation_leg_link WHERE reservation_id = ? AND leg_id = ?");
    $stmt->execute([$reservation_id, $leg_id]);
    header("Location: view.php?id=" . $reservation_id);
    exit;
}

if (isset($_GET['unlink_payment'])) {
    $payment_id = $_GET['unlink_payment'];
    $stmt = $pdo->prepare("DELETE FROM reservation_payment_link WHERE reservation_id = ? AND payment_id = ?");
    $stmt->execute([$reservation_id, $payment_id]);
    header("Location: view.php?id=" . $reservation_id);
    exit;
}

// Fetch reservation details
$stmt = $pdo->prepare("SELECT r.*, p.first_name, p.last_name, p.email_address, ba.agent_name 
                       FROM reservation r 
                       LEFT JOIN passenger p ON r.passenger_id = p.passenger_id 
                       LEFT JOIN booking_agent ba ON r.agent_id = ba.agent_id 
                       WHERE r.reservation_id = ?");
$stmt->execute([$reservation_id]);
$reservation = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$reservation) {
    die("Reservation not found.");
}

// Fetch linked flight legs using reservation_leg_link
$legs_stmt = $pdo->prepare("SELECT fl.* FROM flight_leg fl 
                            JOIN reservation_leg_link rfl ON fl.leg_id = rfl.leg_id 
                            WHERE rfl.reservation_id = ?");
$legs_stmt->execute([$reservation_id]);
$linked_legs = $legs_stmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch linked payments using reservation_payment_link
$pay_stmt = $pdo->prepare("SELECT pay.* FROM payment pay 
                           JOIN reservation_payment_link rp ON pay.payment_id = rp.payment_id 
                           WHERE rp.reservation_id = ?");
$pay_stmt->execute([$reservation_id]);
$linked_payments = $pay_stmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch available legs and payments with safe fallbacks
$all_legs = $pdo->query("SELECT leg_id, flight_number FROM flight_leg")->fetchAll(PDO::FETCH_ASSOC);
$all_payments = $pdo->query("SELECT payment_id, payment_amount FROM payment")->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Reservation Details - HarborWing Air</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 40px; background: #f4f7f6; }
        h1, h2 { color: #004080; }
        .card { background: white; padding: 20px; border-radius: 5px; margin-bottom: 20px; box-shadow: 0 2px 5px rgba(0,0,0,0.1); }
        table { width: 100%; border-collapse: collapse; background: white; margin-top: 10px; }
        th, td { padding: 10px; border: 1px solid #ddd; text-align: left; }
        th { background: #004080; color: white; }
        .btn { display: inline-block; padding: 6px 10px; background: #004080; color: white; text-decoration: none; border-radius: 4px; }
        .btn-danger { background: #cc0000; }
        form { margin-top: 10px; display: inline-block; }
        select, button { padding: 6px; }
        p.error { color: red; font-weight: bold; }
        .warning { color: #b8860b; font-weight: bold; }
    </style>
</head>
<body>

    <h1>Reservation Details #<?php echo htmlspecialchars($reservation['reservation_id']); ?></h1>
    <p><a href="index.php">← Back to Reservations</a></p>

    <?php if($message): ?><p class="error"><?php echo htmlspecialchars($message); ?></p><?php endif; ?>

    <div class="card">
        <h2>Reservation Information</h2>
        <p><strong>Passenger:</strong> <?php echo htmlspecialchars(($reservation['first_name'] ?? '') . ' ' . ($reservation['last_name'] ?? 'Guest')); ?></p>
        <p><strong>Email Address:</strong> <?php echo htmlspecialchars($reservation['email_address'] ?? 'N/A'); ?></p>
        <p><strong>Booking Agent:</strong> <?php echo htmlspecialchars($reservation['agent_name'] ?? 'Direct Booking'); ?></p>
        <p><strong>Status Code:</strong> <?php echo htmlspecialchars($reservation['reservation_status_code']); ?></p>
        <p><strong>Travel Class Code:</strong> <?php echo htmlspecialchars($reservation['travel_class_code']); ?></p>
        <p><strong>Ticket Type Code:</strong> <?php echo htmlspecialchars($reservation['ticket_type_code']); ?></p>
        <p><strong>Party Size:</strong> <?php echo htmlspecialchars($reservation['number_in_party']); ?></p>
    </div>

    <div class="card">
        <h2>Linked Flight Legs</h2>
        <table>
            <tr>
                <th>Leg ID</th>
                <th>Flight Number</th>
                <th>Action</th>
            </tr>
            <?php if (count($linked_legs) > 0): ?>
                <?php foreach ($linked_legs as $leg): ?>
                <tr>
                    <td><?php echo htmlspecialchars($leg['leg_id']); ?></td>
                    <td><?php echo htmlspecialchars($leg['flight_number']); ?></td>
                    <td><a href="view.php?id=<?php echo $reservation_id; ?>&unlink_leg=<?php echo $leg['leg_id']; ?>" class="btn btn-danger" onclick="return confirm('Remove this flight leg link?');">Remove Link</a></td>
                </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr><td colspan="3">No flight legs linked yet.</td></tr>
            <?php endif; ?>
        </table>

        <?php if (count($all_legs) > 0): ?>
        <form method="POST">
            <select name="leg_id" required>
                <option value="">Select Flight Leg to Link</option>
                <?php foreach ($all_legs as $al): ?>
                    <option value="<?php echo $al['leg_id']; ?>">Leg #<?php echo $al['leg_id']; ?> (Flight <?php echo $al['flight_number']; ?>)</option>
                <?php endforeach; ?>
            </select>
            <button type="submit" name="add_leg" class="btn">Link Flight Leg</button>
        </form>
        <?php else: ?>
            <p class="warning">⚠️ No flight legs found in the database. Please create a flight leg first!</p>
        <?php endif; ?>
    </div>

    <div class="card">
        <h2>Linked Payments</h2>
        <table>
            <tr>
                <th>Payment ID</th>
                <th>Amount</th>
                <th>Action</th>
            </tr>
            <?php if (count($linked_payments) > 0): ?>
                <?php foreach ($linked_payments as $pay): ?>
                <tr>
                    <td><?php echo htmlspecialchars($pay['payment_id']); ?></td>
                    <td>$<?php echo number_format((float)$pay['payment_amount'], 2); ?></td>
                    <td><a href="view.php?id=<?php echo $reservation_id; ?>&unlink_payment=<?php echo $pay['payment_id']; ?>" class="btn btn-danger" onclick="return confirm('Remove this payment link?');">Remove Link</a></td>
                </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr><td colspan="3">No payments linked yet.</td></tr>
            <?php endif; ?>
        </table>

        <?php if (count($all_payments) > 0): ?>
        <form method="POST">
            <select name="payment_id" required>
                <option value="">Select Payment to Link</option>
                <?php foreach ($all_payments as $ap): ?>
                    <option value="<?php echo $ap['payment_id']; ?>">Payment #<?php echo $ap['payment_id']; ?> ($<?php echo $ap['payment_amount']; ?>)</option>
                <?php endforeach; ?>
            </select>
            <button type="submit" name="add_payment" class="btn">Link Payment</button>
        </form>
        <?php else: ?>
            <p class="warning">⚠️ No payments found in the database. Please create a payment first!</p>
        <?php endif; ?>
    </div>

</body>
</html>