<?php
session_start();
require 'db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    die("Invalid request");
}

$first  = trim($_POST['first_name']);
$last   = trim($_POST['last_name']);
$email  = trim($_POST['email']);
$mobile = trim($_POST['mobile']);
$msg    = trim($_POST['message']);

if (!$first || !$last || !$email || !$msg) {
    die("Please fill all fields");
}

$sql = "INSERT INTO contact_messages 
(first_name, last_name, email, mobile, message) 
VALUES ($1, $2, $3, $4, $5)";

pg_query_params($conn, $sql, [$first, $last, $email, $mobile, $msg]);

echo "<script>alert('Message sent successfully ✅'); window.location='contactus.html';</script>";
?>