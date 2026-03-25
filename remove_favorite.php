<?php
session_start();
require 'db.php';

if (!isset($_SESSION['user'])) {
    die("Session Expired..Please Login Again");
}

if (!isset($_POST['favorite_id'])) {
    die("No favorite selected!");
}

$favorite_id = intval($_POST['favorite_id']);
$email=$_SESSION['user'];
// Get user_id of logged-in user
$user_res = pg_query_params($conn, "SELECT id FROM users WHERE email=$1 LIMIT 1", [$email]);
$user = pg_fetch_assoc($user_res);
if(!$user)
    {
        die("User Not Found...");
    }
$user_id = $user['id'];

// Delete the selected favorite
pg_query_params($conn, "DELETE FROM favorites WHERE id=$1 AND user_id=$2", [$favorite_id, $user_id]);

header('Location: profile.php');
exit;
?>