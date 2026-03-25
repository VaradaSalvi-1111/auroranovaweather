<?php
session_start();
require 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    $result = pg_query_params(
        $conn,
        "SELECT * FROM admin WHERE username = $1",
        [$username]
    );

    if ($result && pg_num_rows($result) === 1) {
        $admin = pg_fetch_assoc($result);

        if (password_verify($password, $admin['password_hash'])) {

            $_SESSION['is_admin'] = true;
            $_SESSION['admin_id'] = $admin['id'];

            header("Location: view_msg.php");
            exit;
        }
    }

    echo "<script>alert('Invalid admin credentials'); window.location='index.html';</script>";
}