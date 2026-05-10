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
        <div class="auth-card register-card">
            <h2>Join the Scene</h2>

            <form id="regForm">

                <div class="form-group">
                    <label>First Name</label>
                    <input type="text" id="fName" name="fName" maxlength="20" required>
                </div>

                <div class="form-group">
                    <label>Last Name</label>
                    <input type="text" id="lName" name="lName" maxlength="20" required>
                </div>

                <div class="form-group">
                    <label>Email</label>
                    <input type="email" id="email" name="email" maxlength="50" required>
                </div>

                <div class="form-group">
                    <label>Password</label>
                    <input type="password" id="password" name="password" required>
                </div>

                <button type="submit" class="btn">CREATE ACCOUNT</button>
            </form>

            <a href="login.php" class="auth-link">Already have an account? Login.</a>
        </div>
    </div>
</div>

<script src="../scripts/service.js"></script>

<script>
$('#regForm').submit(function(e){
    e.preventDefault();
    registerFunc(this);
});
</script>

</body>
</html>