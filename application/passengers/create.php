<?php
require_once '../db.php';

$message = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $fname = trim($_POST['first_name'] ?? '');
    $sname = trim($_POST['second_name'] ?? '');
    $lname = trim($_POST['last_name'] ?? '');
    $phone = trim($_POST['phone_number'] ?? '');
    $email = trim($_POST['email_address'] ?? '');
    $city = trim($_POST['city'] ?? '');
    $country = trim($_POST['country'] ?? '');

    if (!empty($fname) && !empty($lname) && !empty($email)) {
        try {
            $stmt = $pdo->prepare("INSERT INTO passenger (first_name, second_name, last_name, phone_number, email_address, city, country) VALUES (?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([$fname, $sname ?: null, $lname, $phone ?: null, $email, $city ?: null, $country ?: null]);
            header("Location: index.php");
            exit;
        } catch (PDOException $e) {
            $message = "Error: " . $e->getMessage();
        }
    } else {
        $message = "First name, last name, and email address are required.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Add Passenger</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 40px; background: #f4f7f6; }
        form { background: white; padding: 20px; border-radius: 5px; width: 400px; box-shadow: 0 2px 5px rgba(0,0,0,0.1); }
        input, button { width: 100%; padding: 10px; margin-top: 10px; box-sizing: border-box; }
        button { background: #004080; color: white; border: none; cursor: pointer; }
        button:hover { background: #003366; }
        p { color: red; font-weight: bold; }
    </style>
</head>
<body>
    <h2>Add New Passenger</h2>
    <?php if($message): ?><p><?php echo htmlspecialchars($message); ?></p><?php endif; ?>
    
    <form method="POST">
        <label>First Name:</label>
        <input type="text" name="first_name" required>
        
        <label>Second Name (Middle Name):</label>
        <input type="text" name="second_name">

        <label>Last Name:</label>
        <input type="text" name="last_name" required>

        <label>Phone Number:</label>
        <input type="text" name="phone_number">

        <label>Email Address:</label>
        <input type="email" name="email_address" required>

        <label>City:</label>
        <input type="text" name="city">

        <label>Country:</label>
        <input type="text" name="country">
        
        <button type="submit">Save Passenger</button>
    </form>
    
    <p><a href="index.php">← Back to Passengers</a></p>
</body>
</html>