<?php
require_once 'db.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>HarborWing Air - Management Dashboard</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 40px; background: #f4f7f6; }
        h1 { color: #004080; }
        .grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 20px; margin-top: 20px; }
        .card { background: white; padding: 20px; border-radius: 5px; box-shadow: 0 2px 5px rgba(0,0,0,0.1); text-align: center; }
        .card h3 { color: #004080; margin-bottom: 10px; }
        .btn { display: inline-block; padding: 8px 15px; background: #004080; color: white; text-decoration: none; border-radius: 4px; margin-top: 10px; }
        .btn:hover { background: #003366; }
    </style>
</head>
<body>

    <h1>HarborWing Air Management Dashboard</h1>
    <p>Welcome to the EER-compliant database management system. Select a module below to manage records:</p>

    <div class="grid">
        <div class="card">
            <h3>Airports</h3>
            <p>Manage airport locations and codes.</p>
            <a href="airports/index.php" class="btn">Manage Airports</a>
        </div>

        <div class="card">
            <h3>Passengers</h3>
            <p>Manage passenger profiles and details.</p>
            <a href="passengers/index.php" class="btn">Manage Passengers</a>
        </div>

        <div class="card">
            <h3>Booking Agents</h3>
            <p>Manage registered booking agents.</p>
            <a href="booking_agents/index.php" class="btn">Manage Agents</a>
        </div>

        <div class="card">
            <h3>Flight Schedules</h3>
            <p>Manage flight scheduling data.</p>
            <a href="flight_schedules/index.php" class="btn">Manage Schedules</a>
        </div>

        <div class="card">
            <h3>Flight Legs</h3>
            <p>Manage individual flight route segments.</p>
            <a href="flight_legs/index.php" class="btn">Manage Flight Legs</a>
        </div>

        <div class="card">
            <h3>Flight Costs</h3>
            <p>Manage pricing and cost rules.</p>
            <a href="flight_cost/index.php" class="btn">Manage Costs</a>
        </div>

        <div class="card">
            <h3>Payments</h3>
            <p>Manage reservation payment records.</p>
            <a href="payments/index.php" class="btn">Manage Payments</a>
        </div>

        <div class="card">
            <h3>Reservations</h3>
            <p>Manage bookings, party sizes, and links.</p>
            <a href="reservations/index.php" class="btn">Manage Reservations</a>
        </div>
    </div>

</body>
</html>