<?php
require_once '../db.php';

$search = trim($_GET['search'] ?? '');

if (!empty($search)) {
    $stmt = $pdo->prepare("SELECT * FROM flight_cost WHERE flight_number LIKE ? OR aircraft_type_code LIKE ? OR valid_from_date LIKE ? OR valid_to_date LIKE ? OR cost_amount LIKE ?");
    $searchTerm = "%$search%";
    $stmt->execute([$searchTerm, $searchTerm, $searchTerm, $searchTerm, $searchTerm]);
} else {
    $stmt = $pdo->query("SELECT * FROM flight_cost");
}
$costs = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Flight Costs - HarborWing Air</title>
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
    <h2>Flight Costs Management</h2>

    <a href="create.php" class="btn">+ Add New Flight Cost</a>
    <p><a href="../index.php">← Back to Main Dashboard</a></p>

    <!-- Search Form -->
    <form method="GET" class="search-form">
        <input type="text" name="search" placeholder="Search flight number, aircraft, amount..." value="<?php echo htmlspecialchars($search); ?>">
        <button type="submit">Search</button>
        <?php if (!empty($search)): ?>
            <a href="index.php" class="clear-btn">Clear</a>
        <?php endif; ?>
    </form>

    <table>
        <tr>
            <th>Flight Number</th>
            <th>Aircraft Type</th>
            <th>Valid From</th>
            <th>Valid To</th>
            <th>Cost Amount</th>
            <th>Actions</th>
        </tr>
        <?php if (count($costs) > 0): ?>
            <?php foreach ($costs as $cost): ?>
            <tr>
                <td><?php echo htmlspecialchars($cost['flight_number']); ?></td>
                <td><?php echo htmlspecialchars($cost['aircraft_type_code']); ?></td>
                <td><?php echo htmlspecialchars($cost['valid_from_date']); ?></td>
                <td><?php echo htmlspecialchars($cost['valid_to_date']); ?></td>
                <td>$<?php echo number_format((float)$cost['cost_amount'], 2); ?></td>
                <td class="action-links">
                    <a href="edit.php?flight_number=<?php echo urlencode($cost['flight_number']); ?>&aircraft_type_code=<?php echo urlencode($cost['aircraft_type_code']); ?>&valid_from_date=<?php echo urlencode($cost['valid_from_date']); ?>&valid_to_date=<?php echo urlencode($cost['valid_to_date']); ?>">Edit</a>
                    <a href="delete.php?flight_number=<?php echo urlencode($cost['flight_number']); ?>&aircraft_type_code=<?php echo urlencode($cost['aircraft_type_code']); ?>&valid_from_date=<?php echo urlencode($cost['valid_from_date']); ?>&valid_to_date=<?php echo urlencode($cost['valid_to_date']); ?>" class="delete" onclick="return confirm('Are you sure you want to delete this cost record?');">Delete</a>
                </td>
            </tr>
            <?php endforeach; ?>
        <?php else: ?>
            <tr>
                <td colspan="6">No flight costs found.</td>
            </tr>
        <?php endif; ?>
    </table>

</body>
</html>