<?php
session_start();
require 'db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: index.html");
    exit;
}

$user_id = $_SESSION['user_id'];

$query = "
    SELECT id, city, created_at
    FROM favorites
    WHERE user_id = $1
    ORDER BY created_at DESC
";

$result = pg_query_params($conn, $query, [$user_id]);
?>

<!DOCTYPE html>
<html>
<head>
    <title>My Favorites</title>
    <style>
        body {
            background:#0b0b2e;
            color:white;
            font-family:Arial;
        }
        .card {
            background:rgba(255,255,255,0.1);
            padding:15px;
            margin:15px;
            border-radius:10px;
        }
        .remove {
            color:#ff6b6b;
            text-decoration:none;
            font-size:14px;
        }
    </style>
</head>
<body>

<h2>⭐ My Favorite Locations</h2>

<?php if (pg_num_rows($result) == 0): ?>
    <p>No favorite cities added yet.</p>
<?php endif; ?>

<?php while ($row = pg_fetch_assoc($result)): ?>
    <div class="card">
        <b><?= htmlspecialchars($row['city']) ?></b><br>
        <small>Added on <?= $row['created_at'] ?></small><br><br>

        <a class="remove"
           href="remove_favorite.php?id=<?= $row['id'] ?>"
           onclick="return confirm('Remove from favorites?')">
           ❌ Remove
        </a>
    </div>
<?php endwhile; ?>

</body>
</html>