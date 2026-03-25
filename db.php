<?php
$host = "localhost";       // your host
$port = "5432";            // default postgres port
$dbname = "AuroraNova";    // your database name
$user = "postgres";        // your db user
$password = "varada20"; // your db password

$conn = pg_connect("host=$host port=$port dbname=$dbname user=$user password=$password");
if (!$conn) {
    die("Connection failed: " . pg_last_error());
}
?>