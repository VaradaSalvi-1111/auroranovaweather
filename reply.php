<?php  
session_start();  
if(!isset($_SESSION['is_admin']))  
    {  
        header("Location: index.html");  
        exit;  
    }  
require 'admin_auth.php';  
require 'db.php';  
  
$id = $_GET['id'] ?? null;  
if (!$id) die("Invalid request");  
  
if ($_SERVER['REQUEST_METHOD'] === 'POST') {  
    $reply = htmlspecialchars(trim($_POST['reply']));  
  
    pg_query_params(  
        $conn,  
        "UPDATE contact_messages SET admin_reply = $1 WHERE id = $2",  
        [$reply, $id]  
    );  
  
    echo "<script>alert('Reply sent ✅'); window.location='view_msg.php';</script>";  
}  
?>  

<!DOCTYPE html>
<html>
<head>
    <title>Admin Reply</title>
</head>

<body style="
    margin:0;
    height:100vh;
    display:flex;
    justify-content:center;
    align-items:center;
    background:linear-gradient(135deg,#0f2027,#203a43,#2c5364);
    font-family:Arial, Helvetica, sans-serif;
">

    <form method="POST" action="reply.php?id=<?=$id ?>" style="
        background:#111;
        padding:30px;
        width:360px;
        border-radius:14px;
        box-shadow:0 15px 40px rgba(0,0,0,0.6);
        text-align:center;
    ">

        <h3 style="
            color:#00d4ff;
            margin-bottom:20px;
            letter-spacing:1px;
        ">Admin Reply</h3>

        <textarea name="reply" required style="
            width:95%;
            height:140px;
            padding:12px;
            border-radius:10px;
            border:none;
            outline:none;
            resize:none;
            font-size:14px;
            margin-bottom:20px;
        "></textarea>

        <button type="submit" style="
            width:100%;
            padding:12px;
            border:none;
            border-radius:10px;
            background:#00d4ff;
            color:#000;
            font-weight:bold;
            font-size:15px;
            cursor:pointer;
            transition:0.3s;
        "
        onmouseover="this.style.background='#00bcd4'"
        onmouseout="this.style.background='#00d4ff'">
            Send Reply
        </button>

    </form>

</body>
</html>