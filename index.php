<?php session_start(); ?>

<!DOCTYPE html>
<html>
<head>
    <title>Weather forecast</title>
     <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        img {
            height:125%;
            width:100%;
        }
        body {
            margin: 0;
            font-family: Arial, sans-serif;
            color: white;
        }

        /* Splash Screen */
        .splash {
            position: fixed;
            width: 100%;
            height: 100%;
            background: black;
            display: flex;
            justify-content: center;
            align-items: center;
            z-index: 10;
            animation: fadeOut 3s forwards;
            animation-delay: 2s;
        }

        .splash img {
            width: 100%;
        }

        @keyframes fadeOut {
            to {
                opacity: 0;
                visibility: hidden;
            }
        }

    </style>
</head>

<body>
    <!-- Splash Screen -->
    <div class="splash" id="splash">
        <img src="images/intro.png" alt="LuminousVision Logo">
    </div>
    <?php if (isset($_SESSION['error'])): ?>
    <script>alert("<?= $_SESSION['error']; ?>");</script>
    <?php unset($_SESSION['error']); ?>
<?php endif; ?>

<?php if (isset($_SESSION['success'])): ?>
    <script>alert("<?= $_SESSION['success']; ?>");</script>
    <?php unset($_SESSION['success']); ?>
<?php endif; ?>
</body>
</html>


<!--Login animation-->
<!DOCTYPE html>
<html lang="en" >
<head>
  <meta charset="UTF-8">
  <title>Animated Login &amp; Register Form</title>
  <link rel="stylesheet" href="./style.css">

</head>
<body>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login & Signup Form</title>
    <link rel="stylesheet" href="style.css">

</head>
<body>
    <div class="container">
        <div class="curved-shape"></div>
        <div class="curved-shape2"></div>
        <div class="form-box Login">
            <h2 class="animation" style="--D:0; --S:21">Login</h2>
            <form action="login.php"  method="POST">
                <div class="input-box animation" style="--D:1; --S:22">
                    <input type="text"  name="username" required>
                    <label for="">Username</label>
                    <box-icon type='solid' name='user' color="gray"></box-icon>
                </div>

                <div class="input-box animation" style="--D:2; --S:23">
                    <input type="password"  name="password" required>
                    <label for="">Password</label>
                    <box-icon name='lock-alt' type='solid' color="gray"></box-icon>
                </div>

                <div class="input-box animation" style="--D:3; --S:24">
                    <button class="btn" type="submit" >User Login</button>
                </div>

                <div class="regi-link animation" style="--D:4; --S:25">
                    <p>Don't have an account? <br> <a href="#" class="SignUpLink">Sign Up</a></p>
                </div>
                <div class="input-box animation" style="--D:3; --S:24">
                    <button class="btn" type="submit"><a href="admin_login.html" style="text-decoration:none;">Admin Login</a>
                </button>
                </div>
    
            </form>
        </div>

        <div class="info-content Login">
            <h2 class="animation" style="--D:0; --S:20">WELCOME BACK!</h2>
            <p class="animation" style="--D:1; --S:21">We are happy to have you with us again. If you need anything, we are here to help.</p>
        </div>

        <div class="form-box Register">
            <h2 class="animation" style="--li:17; --S:0">Register</h2>
            <form action="register.php" method="POST">
                <div class="input-box animation" style="--li:18; --S:1">
                    <input type="text"  name="username" required>
                    <label for="">Username</label>
                    <box-icon type='solid' name='user' color="gray"></box-icon>
                </div>

                <div class="input-box animation" style="--li:19; --S:2">
                    <input type="email"  name="email" required>
                    <label for="">Email</label>
                    <box-icon name='envelope' type='solid' color="gray"></box-icon>
                </div>

                <div class="input-box animation" style="--li:19; --S:3">
                    <input type="password"  name="password" required>
                    <label for="">Password</label>
                    <box-icon name='lock-alt' type='solid' color="gray"></box-icon>
                </div>

                <div class="input-box animation" style="--li:20; --S:4">
                    <button class="btn" type="submit">Register</button>
                </div>

                <div class="regi-link animation" style="--li:21; --S:5">
                    <p>Don't have an account? <br> <a href="#" class="SignInLink">Sign In</a></p>
                </div>
            </form>
        </div>

        <div class="info-content Register">
            <h2 class="animation" style="--li:17; --S:0">WELCOME!</h2>
            <p class="animation" style="--li:18; --S:1">We’re delighted to have you here. If you need any assistance, feel free to reach out.</p>
        </div>

    </div>

    <script src="index.js"></script>
    <script src="https://unpkg.com/boxicons@2.1.4/dist/boxicons.js"></script>

</body>
</html>
  <script  src="./script.js"></script>

</body>
</html>