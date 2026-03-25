<?php
session_start();
require_once 'db.php'; // database connection

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    // Get and trim inputs
    $username = trim($_POST['username'] ?? '');
    $email    = trim($_POST['email'] ?? '');
    $password = trim($_POST['password'] ?? '');

    // Check if any field is empty
    if (empty($username) || empty($email) || empty($password)) {
        $_SESSION['error'] = "Please fill out all fields!";
        header("Location: index.html"); // redirect back to register form
        exit;
    }

    // Validate email
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $_SESSION['error'] = "Invalid email format!";
        header("Location: index.html");
        exit;
    }

    // Hash the password securely
    $passwordHash = password_hash($password, PASSWORD_DEFAULT);

    // Insert user into PostgreSQL
    $sql = "INSERT INTO users (username, email, password_hash) VALUES ($1, $2, $3)";
    $result = pg_query_params($conn, $sql, [$username, $email, $passwordHash]);

    if ($result) {
        $_SESSION['success'] = "Registration successful! You can now login.";
        header("Location: index.html");
        exit;
    } else {
        $_SESSION['error'] = "Registration failed: " . pg_last_error($conn);
        header("Location: index.html");
        exit;
    }
} else {
    header("Location: index.html");
    exit;
}
?>