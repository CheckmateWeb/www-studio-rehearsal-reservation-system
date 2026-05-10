<?php
session_start();

if (isset($_SESSION['user_id'])) {
    if ($_SESSION['email'] === "MarivelesAdmin@gmail.com") {
        header("Location: admin.php");
    } else {
        header("Location: dashboard.php");
    }
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <title>Studio Login</title>
    <link rel="stylesheet" href="../Designs/style.css">
</head>

<body>
<div class="auth-container">
    <div class="auth-card">
        <h2>Vibe Check</h2>

        <form id="loginForm">
            <div class="input-field">
                <label for="email">Email</label>
                <input id="email" type="email" required>
            </div>

            <div class="input-field">
                <label for="password">Password</label>
                <input id="password" type="password" required>
            </div>

            <button type="submit" class="btn">
                ENTER STUDIO
            </button>
        </form>

        <a href="registration.php" class="auth-link">
            New band? Register here.
        </a>
    </div>
</div>

  <footer class="footer">
        <div class="footer-container">
            <div class="footer-left">
                <h2>StudioRehearsal</h2>
                <p>Your space. Your sound. Your moment.</p>
            </div>
        </div>

        <div class="footer-bottom">
            <p>© 2026 StudioRehearsal. All rights reserved.</p>
        </div>
    </footer>

<script src="../scripts/service.js"></script>

<script>
    $('#loginForm').submit(function(e){
        e.preventDefault();
        loginFunc();
    });
</script>

</body>
</html>