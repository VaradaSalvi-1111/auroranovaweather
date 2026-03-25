<?php
session_start();
require 'db.php';

// Redirect if not logged in
if (!isset($_SESSION['user'])) {
    header('Location: index.html');
    exit;
}

$username = $_SESSION['user'];

// Fetch user info
$result = pg_query_params($conn, "SELECT id, username, email FROM users WHERE email=$1 LIMIT 1", [$username]);
if (!$result || pg_num_rows($result) === 0) {
    die("User not found!");
}
$user = pg_fetch_assoc($result);

// Fetch favorites for this user using user_id
$fav_result = pg_query_params($conn, "SELECT id, city FROM favorites WHERE user_id=$1 ORDER BY created_at DESC", [$user['id']]);
$favorites = pg_fetch_all($fav_result); // returns array of favorites
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>My Profile</title>
<style>
    body { background: url("images/bg.jpeg") no-repeat center center fixed; background-size: cover; margin:0; font-family:Arial,sans-serif; display:flex; justify-content:center; align-items:center; height:100vh; color:white; }
    .card { width:360px; background:#0f2a52; border-radius:18px; padding:18px; box-shadow:0 0 12px skyblue; }
    .header { display:flex; align-items:center; gap:12px; margin-bottom:20px; }
    .back { font-size:20px; cursor:pointer; }
    .header h2 { font-size:18px; margin:0; }
    .profile { display:flex; align-items:center; gap:14px; padding-bottom:15px; border-bottom:1px solid rgba(255,255,255,0.15); margin-bottom:10px; }
    .avatar { width:55px; height:55px; border-radius:50%; background:#1a3b6f; display:flex; justify-content:center; align-items:center; font-size:26px; }
    .menu { margin-top:10px; }
    .menu-item { display:flex; align-items:center; justify-content:space-between; padding:12px 5px; font-size:15px; cursor:pointer; }
    .menu-left { display:flex; align-items:center; gap:12px; }
    .icon { width:22px; text-align:center; font-size:16px; color:#4da3ff; }
    .arrow { color:#9bbcff; font-size:18px; }
    .logout { color:#ff6b6b; }
    select { padding:5px; font-size:14px; }
    button { padding:5px 8px; font-size:13px; margin-left:5px; cursor:pointer; background:red; color:white; border:none; border-radius:4px; }
</style>
</head>
<body>

<div class="card">

    <div class="header">
        <div class="back" onclick="window.location='home.php'">⬅️</div>
        <h2>My Profile</h2>
    </div>

    <div class="profile">
        <div class="avatar"><?= strtoupper($user['username'][0]) ?></div>
        <div>
            <h3><?= htmlspecialchars($user['username']) ?></h3>
            <p><?= htmlspecialchars($user['email']) ?></p>
        </div>
    </div>

    <div class="menu">

        <!-- Favorites dropdown -->
        <div class="menu-item">
            <div class="menu-left">
                <div class="icon">❤️</div>
                <span>Favourites:</span>
                <form method="post" action="remove_favorites.php" style="display:flex; align-items:center;">
                    <select name="favorite_id">
                        <?php if ($favorites): ?>
                            <?php foreach($favorites as $fav): ?>
                                <option value="<?= $fav['id'] ?>"><?= htmlspecialchars($fav['city']) ?></option>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <option disabled>No favorites</option>
                        <?php endif; ?>
                    </select>
                    <?php if ($favorites): ?>
                        <button type="submit">Remove</button>
                    <?php endif; ?>
                </form>
            </div>
        </div>

        <div class="menu-item" onclick="window.location='about.php'">
            <div class="menu-left">
                <div class="icon">ℹ️</div>
                <span>About App</span>
            </div>
            <div class="arrow">></div>
        </div>

        <div class="menu-item" onclick="window.location='logout.php'">
            <div class="menu-left">
                <div class="icon logout">🚫</div>
                <span class="logout">Log Out</span>
            </div>
            <div class="arrow">></div>
        </div>

    </div>

</div>

</body>
</html>