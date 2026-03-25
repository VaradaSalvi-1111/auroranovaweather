<!--this will block non admins-->
<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['is_admin'])) {
    header("Location: index.html");
    exit;
}
?>