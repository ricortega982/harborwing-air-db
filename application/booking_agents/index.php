<?php
require_once '../db.php';

$search = trim($_GET['search'] ?? '');

if (!empty($search)) {
    $stmt = $pdo->prepare("SELECT * FROM booking_agent WHERE agent_id LIKE ? OR agent_name LIKE ? OR additional_details LIKE ?");
    $searchTerm = "%$search%";
    $stmt->execute([$searchTerm, $searchTerm, $searchTerm]);
} else {
    $stmt = $pdo->query("SELECT * FROM booking_agent");
}
$agents = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Booking Agents - HarborWing Air</title>
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
    <h2>Booking Agents Management</h2>

    <a href="create.php" class="btn">+ Add New Booking Agent</a>
    <p><a href="../index.php">← Back to Main Dashboard</a></p>

    <!-- Search Form -->
    <form method="GET" class="search-form">
        <input type="text" name="search" placeholder="Search agent ID, name, details..." value="<?php echo htmlspecialchars($search); ?>">
        <button type="submit">Search</button>
        <?php if (!empty($search)): ?>
            <a href="index.php" class="clear-btn">Clear</a>
        <?php endif; ?>
    </form>

    <table>
        <tr>
            <th>Agent ID</th>
            <th>Name / Agency</th>
            <th>Additional Details</th>
            <th>Actions</th>
        </tr>
        <?php if (count($agents) > 0): ?>
            <?php foreach ($agents as $agent): ?>
            <tr>
                <td><?php echo htmlspecialchars($agent['agent_id']); ?></td>
                <td><?php echo htmlspecialchars($agent['agent_name']); ?></td>
                <td><?php echo htmlspecialchars($agent['additional_details'] ?? 'N/A'); ?></td>
                <td class="action-links">
                    <a href="edit.php?id=<?php echo urlencode($agent['agent_id']); ?>">Edit</a>
                    <a href="delete.php?id=<?php echo urlencode($agent['agent_id']); ?>" class="delete" onclick="return confirm('Are you sure you want to delete this booking agent?');">Delete</a>
                </td>
            </tr>
            <?php endforeach; ?>
        <?php else: ?>
            <tr>
                <td colspan="4">No booking agents found.</td>
            </tr>
        <?php endif; ?>
    </table>

</body>
</html>