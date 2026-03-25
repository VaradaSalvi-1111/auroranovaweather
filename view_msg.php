<?php
session_start();
if(!isset($_SESSION['is_admin']))
    {
        header("Location: index.html");
        exit;
    }
require 'admin_auth.php';
require 'db.php';
$result = pg_query($conn, "SELECT * FROM contact_messages ORDER BY id DESC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin | User Messages</title>
</head>

<body style="
    margin:0;
    min-height:100vh;
    background:linear-gradient(135deg,#0f2027,#203a43,#2c5364);
    font-family:Arial, Helvetica, sans-serif;
    padding:30px;
">

    <h2 style="
        text-align:center;
        color:#00d4ff;
        margin-bottom:25px;
        letter-spacing:1px;
    ">
        User Messages
    </h2>

    <div style="
        max-width:1100px;
        margin:auto;
        background:#111;
        padding:20px;
        border-radius:14px;
        box-shadow:0 15px 40px rgba(0,0,0,0.6);
        overflow-x:auto;
    ">

        <table border="0" cellpadding="10" cellspacing="0" style="
            width:100%;
            border-collapse:collapse;
            color:#fff;
            font-size:14px;
        ">

            <tr style="background:#00d4ff; color:#000;">
                <th style="border-radius:10px 0 0 10px; padding:10px; text-align:left;">Name</th>
                <th style="padding:10px; text-align:left;">Email</th>
                <th style="padding:10px; text-align:left;">Message</th>
                <th style="padding:10px; text-align:center;">Status</th>
                <th style="border-radius:0 10px 10px 0;padding:10px; text-align:center;">Action</th>
            </tr>
            
            <?php while ($row = pg_fetch_assoc($result)) { ?>
            <tr style="border-bottom:2px solid #333;">
                <td style="padding:14px;">
                    <?= htmlspecialchars($row['first_name'].' '.$row['last_name']) ?>
                </td>

                <td style="padding:10px;">
                    <?= htmlspecialchars($row['email']) ?>
                </td>

                <td style="padding:10px; max-width:400px;">
                    <?= htmlspecialchars($row['message']) ?>
                </td>

                <td style="padding:10px; text-align:center;">
                    <?= $row['admin_reply'] ? '✅ Replied' : '❌ Pending' ?>
                </td>

                <td style="padding:10px; text-align:center;">
                    <a href="reply.php?id=<?=$row['id'] ?>" style="
                        padding:6px 14px;
                        background:#00d4ff;
                        color:#000;
                        text-decoration:none;
                        border-radius:20px;
                        font-size:13px;
                        font-weight:bold;
                        transition:0.3s;
                    "
                    onmouseover="this.style.background='#00bcd4'"
                    onmouseout="this.style.background='#00d4ff'">
                        Reply
                    </a>
                </td>
            </tr>
            <?php } ?>

        </table>
    </div>

</body>
</html>