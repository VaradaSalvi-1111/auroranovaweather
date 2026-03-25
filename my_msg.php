<?php
session_start();
require 'db.php';

if (!isset($_SESSION['user'])) {
    die("Please login first");
}

$email = $_SESSION['user'];

$query = "
SELECT message, admin_reply, created_at
FROM contact_messages
WHERE email = $1
ORDER BY created_at DESC
";

$result = pg_query_params($conn, $query, [$email]);

if(!$result)
{
    die("Query Failed;".pg_last_error($conn));
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>My Messages</title>
    <style>
        body {
            background:#0b0b2e;
            color:white;
            font-family:Arial;
        }
        .box {
            background:rgba(255,255,255,0.1);
            padding:15px;
            margin:15px;
            border-radius:10px;
        }
        .admin {
            color:#00ffcc;
        }
    </style>
</head>
<body>

<h2>📩 My Messages</h2>

<?php
if (pg_num_rows($result) == 0) {
    echo "<p>No messages yet.</p>";
}

while ($row = pg_fetch_assoc($result)) {
    echo "<div class='box'>";
    echo "<p><b>You:</b> ".htmlspecialchars($row['message'])."</p>";

    if ($row['admin_reply']) {
        echo "<p class='admin'><b>Admin:</b> ".htmlspecialchars($row['admin_reply'])."</p>";
    } else {
        echo "<p class='admin'><b>Admin:</b> Pending reply</p>";
    }

    echo "<small>".$row['created_at']."</small>";
    echo "</div>";
}
?>

</body>
</html>