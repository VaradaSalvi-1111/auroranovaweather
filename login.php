<?php
session_start();
require 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $password = $_POST['password'];

    $query = "SELECT * FROM users WHERE username = $1";
    $result = pg_query_params($conn, $query, [$username]);
    $user = pg_fetch_assoc($result);

    if ($user && password_verify($password, $user['password_hash'])) {
        // Store necessary info in session
        $_SESSION['user_id'] = $user['id'];        // user ID
        $_SESSION['user'] = $user['email'];     // username
        $_SESSION['user_email'] = $user['email'];  // email for sending weather report

        header('Location: home.php');
        exit;
    } else {
        echo "<script>alert('Invalid username or password'); window.location='index.html';</script>";
    }
}
?>