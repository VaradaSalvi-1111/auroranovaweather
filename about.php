<?php
session_start();
require 'db.php';

// Check if user is logged in
if (!isset($_SESSION['user'])) {
    header('Location: index.html');
    exit;
}

// Fetch user info (optional, if you want to display username)
$username = $_SESSION['user'];
$res = pg_query_params($conn, "SELECT username, email FROM users WHERE username=$1 LIMIT 1", [$username]);
$user = pg_fetch_assoc($res);
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>About AuroraNova</title>
<style>
    body {
        background: url("images/bg.jpeg") no-repeat center center fixed;
        background-size: cover;
        margin: 0;
        font-family: Arial, sans-serif;
        display: flex;
        justify-content: center;
        align-items: center;
        height: 100vh;
        color: white;
    }

    .card {
        width: 360px;
        background: #0f2a52;
        border-radius: 18px;
        padding: 20px;
        box-shadow: 0 0 12px skyblue,0 0 12px skyblue,0 0 12px skyblue;
    }

    .header {
        display: flex;
        align-items: center;
        gap: 12px;
        margin-bottom: 20px;
    }

    .back {
        font-size: 20px;
        cursor: pointer;
    }

    .header h2 {
        font-size: 18px;
        margin: 0;
    }

    .content {
        font-size: 14px;
        line-height: 1.5;
        color: #cfd9ff;
    }

    .footer {
        margin-top: 20px;
        font-size: 12px;
        text-align: center;
        color: #9bbcff;
    }
</style>
</head>
<body>

<div class="card">

    <!-- Header -->
    <div class="header">
        <div class="back" onclick="window.location='home.php'">⬅️</div>
        <h2>About AuroraNova</h2>
    </div>

    <!-- Content -->
    <div class="content">
        <p><b>AuroraNova Weather App</b> is your personal weather assistant. It provides accurate weather updates, air quality info, UV index, and hourly & daily forecasts for any city worldwide.</p>

        <p>Features include:</p>
        <ul>
            <li>Live temperature, humidity, wind, and pressure updates</li>
            <li>Hourly and 6-day forecasts</li>
            <li>Air quality (AQI) and UV index alerts</li>
            <li>Save your favorite locations for quick access</li>
            <li>Interactive weather maps</li>
        </ul>

        <p>Our mission is to give you reliable weather information so you can plan your day safely and efficiently.</p>
    </div>

    <!-- Footer -->
    <div class="footer">
        &copy; <?= date('Y') ?> AuroraNova. All rights reserved.
    </div>

</div>

</body>
</html>