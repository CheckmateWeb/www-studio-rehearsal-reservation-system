<?php
session_start();

if (isset($_SESSION['user_id'])) {
    header("Location: dashboard.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Join the Scene</title>

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <link rel="stylesheet" href="../Designs/style.css">
</head>
<body>

<div class="main-content">
    <div class="auth-container">
        <div class="auth-layout">
            <section class="auth-showcase">
                <div>
                    <span class="brand-chip">Create Your Access</span>
                    <div class="auth-copy">
                        <h1 class="auth-title">Start your studio workflow on a cleaner system.</h1>
                        <p class="auth-description">
                            Register once, reserve faster, and keep your rehearsal sessions organized from a single dashboard.
                        </p>
                    </div>
                </div>

                <div class="showcase-points">
                    <div class="showcase-point">
                        <strong>Simple registration</strong>
                        <span>Clean fields, faster onboarding, and less friction.</span>
                    </div>
                    <div class="showcase-point">
                        <strong>Instant booking access</strong>
                        <span>Go from sign up to room reservation without extra pages.</span>
                    </div>
                    <div class="showcase-point">
                        <strong>Admin-ready backend</strong>
                        <span>Your account structure stays connected to the updated admin view.</span>
                    </div>
                </div>
            </section>

            <div class="auth-card register-card">
                <span class="auth-kicker">New Account</span>
                <h2>Register</h2>
                <p class="auth-subtext">Create a profile so you can start booking rehearsal rooms and tracking reservations.</p>

                <form id="regForm">
                    <div class="form-row">
                        <div class="form-group">
                            <label for="fName">First Name</label>
                            <input type="text" id="fName" name="fName" maxlength="20" autocomplete="given-name" placeholder="First name" required>
                        </div>

                        <div class="form-group">
                            <label for="lName">Last Name</label>
                            <input type="text" id="lName" name="lName" maxlength="20" autocomplete="family-name" placeholder="Last name" required>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="email">Email</label>
                        <input type="email" id="email" name="email" maxlength="50" autocomplete="email" placeholder="you@example.com" required>
                    </div>

                    <div class="form-group">
                        <label for="password">Password</label>
                        <input type="password" id="password" name="password" maxlength="64" autocomplete="new-password" placeholder="Create a secure password" required>
                        <div class="password-guide" id="passwordGuide" aria-live="polite">
                            <p class="password-guide-title">Password guide</p>
                            <div class="password-rule" id="passwordRuleIdentifiers">
                                <span class="password-rule-indicator" aria-hidden="true"></span>
                                <span>8 to 64 characters and includes both a letter and a number</span>
                            </div>
                            <div class="password-rule" id="passwordRuleSpecial">
                                <span class="password-rule-indicator" aria-hidden="true"></span>
                                <span>Includes at least one special character like @, #, $, or %</span>
                            </div>
                        </div>
                    </div>

                    <button type="submit" class="btn">Create Account</button>
                </form>

                <a href="login.php" class="auth-link">Already have an account? Login.</a>
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
function updatePasswordGuide() {
    const passwordInput = document.getElementById('password');
    const passwordValue = passwordInput.value;
    const checklist = getPasswordChecklist(passwordValue);

    document.getElementById('passwordRuleIdentifiers').classList.toggle(
        'is-valid',
        checklist.hasMinimumLength && checklist.hasLetterAndNumber
    );
    document.getElementById('passwordRuleSpecial').classList.toggle(
        'is-valid',
        checklist.hasSpecialCharacter
    );
}

document.getElementById('password').addEventListener('input', updatePasswordGuide);
updatePasswordGuide();

$('#regForm').submit(function(e){
    e.preventDefault();
    registerFunc(this);
});
</script>

</body>
</html>
