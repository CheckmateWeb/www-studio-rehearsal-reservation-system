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
<div class="main-content">
    <div class="auth-container">
        <div class="auth-layout">
            <section class="auth-showcase">
                <div>
                    <span class="brand-chip">StudioRehearsal</span>
                    <div class="auth-copy">
                        <h1 class="auth-title">Book fast. Rehearse clean. Stay in sync.</h1>
                        <p class="auth-description">
                            A cleaner studio booking experience for bands, solo artists, and managers who want a simple flow from sign in to reservation.
                        </p>
                    </div>
                </div>

                <div class="showcase-points">
                    <div class="showcase-point">
                        <strong>Fast room selection</strong>
                        <span>See available spaces and reserve in a few clicks.</span>
                    </div>
                    <div class="showcase-point">
                        <strong>Consistent admin flow</strong>
                        <span>Monitor bookings, users, and room performance from one place.</span>
                    </div>
                    <div class="showcase-point">
                        <strong>Modal actions preserved</strong>
                        <span>Your current popup flow still stays active on every action.</span>
                    </div>
                </div>
            </section>

            <div class="auth-card">
                <span class="auth-kicker">Welcome Back</span>
                <h2>Login</h2>
                <p class="auth-subtext">Access your bookings, reserve rooms, and manage sessions without extra clutter.</p>

                <form id="loginForm">
                    <div class="input-field">
                        <label for="email">Email</label>
                        <input id="email" type="email" autocomplete="email" inputmode="email" maxlength="50" placeholder="you@example.com" required>
                    </div>

                    <div class="input-field">
                        <label for="password">Password</label>
                        <input id="password" type="password" autocomplete="current-password" maxlength="64" placeholder="Enter your password" required>
                    </div>

                    <button type="submit" class="btn">
                        Enter Studio
                    </button>
                </form>

                <a href="registration.php" class="auth-link">
                    New here? Create your account.
                </a>
            </div>
        </div>
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
        <p>&copy; 2026 StudioRehearsal. All rights reserved.</p>
    </div>
</footer>

<script src="../scripts/service.js"></script>

<script>
    (function () {
        var emailInput = document.getElementById('email');
        var passwordInput = document.getElementById('password');

        emailInput.addEventListener('input', function () {
            this.value = this.value.replace(/\s+/g, '').slice(0, 50);
        });

        passwordInput.addEventListener('input', function () {
            if (this.value.length > 64) {
                this.value = this.value.slice(0, 64);
            }
        });
    }());

    $('#loginForm').submit(function(e){
        e.preventDefault();
        loginFunc();
    });
</script>

</body>
</html>
