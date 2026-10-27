<?php
require_once '../db.php';

$search = trim($_GET['search'] ?? '');

if (!empty($search)) {
    $query = "SELECT r.*, p.first_name, p.last_name, ba.agent_name 
              FROM reservation r
              LEFT JOIN passenger p ON r.passenger_id = p.passenger_id
              LEFT JOIN booking_agent ba ON r.agent_id = ba.agent_id
              WHERE r.reservation_id LIKE ? 
                 OR p.first_name LIKE ? 
                 OR p.last_name LIKE ? 
                 OR r.reservation_status_code LIKE ? 
                 OR r.travel_class_code LIKE ?";
    $stmt = $pdo->prepare($query);
    $searchTerm = "%$search%";
    $stmt->execute([$searchTerm, $searchTerm, $searchTerm, $searchTerm, $searchTerm]);
} else {
    $query = "SELECT r.*, p.first_name, p.last_name, ba.agent_name 
              FROM reservation r
              LEFT JOIN passenger p ON r.passenger_id = p.passenger_id
              LEFT JOIN booking_agent ba ON r.agent_id = ba.agent_id";
    $stmt = $pdo->query($query);
}
$reservations = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Reservations - HarborWing Air</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 40px; background: #f4f7f6; }
        h1 { color: #004080; }
        .btn { display: inline-block; padding: 8px 12px; background: #004080; color: white; text-decoration: none; border-radius: 4px; margin-bottom: 15px; }
        .btn:hover { background: #003366; }
        .search-form { margin-bottom: 15px; }
        .search-form input[type="text"] { padding: 8px; width: 280px; border: 1px solid #ccc; border-radius: 4px; }
        .search-form button { padding: 8px 12px; background: #004080; color: white; border: none; border-radius: 4px; cursor: pointer; }
        .search-form button:hover { background: #003366; }
        .clear-btn { margin-left: 10px; text-decoration: none; color: #cc0000; font-weight: bold; }
        table { width: 100%; border-collapse: collapse; background: white; margin-top: 10px; box-shadow: 0 2px 5px rgba(0,0,0,0.1); }
        th, td { padding: 12px; border: 1px solid #ddd; text-align: left; }
        th { background: #004080; color: white; }
        tr:hover { background: #f1f1f1; }
        .action-links a { margin-right: 10px; text-decoration: none; color: #004080; font-weight: bold; }
        .action-links a.delete { color: #cc0000; }
    </style>
</head>
<body>

    <h1>HarborWing Air Dashboard</h1>
    <h2>Reservations Management</h2>

    <a href="create.php" class="btn">+ Create New Reservation</a>
    <p><a href="../index.php">← Back to Main Dashboard</a></p>

    <!-- Search Form -->
    <form method="GET" class="search-form">
        <input type="text" name="search" placeholder="Search ID, Passenger, Status..." value="<?php echo htmlspecialchars($search); ?>">
        <button type="submit">Search</button>
        <?php if (!empty($search)): ?>
            <a href="index.php" class="clear-btn">Clear</a>
        <?php endif; ?>
    </form>

    <table>
        <tr>
            <th>ID</th>
            <th>Passenger</th>
            <th>Agent</th>
            <th>Status</th>
            <th>Class</th>
            <th>Party Size</th>
            <th>Actions</th>
        </tr>
        <?php if (count($reservations) > 0): ?>
            <?php foreach ($reservations as $res): ?>
            <tr>
                <td><?php echo htmlspecialchars($res['reservation_id']); ?></td>
                <td><?php echo htmlspecialchars(($res['first_name'] ?? '') . ' ' . ($res['last_name'] ?? 'Guest')); ?></td>
                <td><?php echo htmlspecialchars($res['agent_name'] ?? 'Direct'); ?></td>
                <td><?php echo htmlspecialchars($res['reservation_status_code']); ?></td>
                <td><?php echo htmlspecialchars($res['travel_class_code']); ?></td>
                <td><?php echo htmlspecialchars($res['number_in_party']); ?></td>
                <td class="action-links">
                    <a href="view.php?id=<?php echo urlencode($res['reservation_id']); ?>">View Details & Links</a>
                    <a href="delete.php?id=<?php echo urlencode($res['reservation_id']); ?>" class="delete" onclick="return confirm('Are you sure you want to delete this reservation?');">Delete</a>
                </td>
            </tr>
            <?php endforeach; ?>
        <?php else: ?>
            <tr>
                <td colspan="7">No reservations found.</td>
            </tr>
        <?php endif; ?>
    </table>

</body>
</html>