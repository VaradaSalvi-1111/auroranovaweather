<?php
session_start();
require 'db.php';

if (!isset($_SESSION['user_id'])) {
    die("Please login first");
}

$user_id = $_SESSION['user_id'];
$city = trim($_POST['city'] ?? '');

if ($city === '') {
    die("City not provided");
}

/* Check if already exists */
$checkQuery = "
    SELECT 1 FROM favorites
    WHERE user_id = $1 AND city = $2
";

$checkResult = pg_query_params($conn, $checkQuery, [$user_id, $city]);

if (pg_num_rows($checkResult) > 0) {
    echo "<script>
        alert('City already in favorites ❤️');
        window.location='home.php';
    </script>";
    exit;
}

/* Insert favorite */
$insertQuery = "
    INSERT INTO favorites (user_id, city)
    VALUES ($1, $2)
";

pg_query_params($conn, $insertQuery, [$user_id, $city]);

echo "<script>
    alert('Added to favorites ⭐');
    window.location='home.php';
</script>";
?>