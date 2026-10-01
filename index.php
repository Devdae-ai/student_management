<?php

session_start();

if (isset($_SESSION['student_db_id'])) {
    header("Location: dashboard.php");
    exit();
}

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <title>Student Portal</title>

    <link rel="stylesheet"
          href="assets/css/style.css">

</head>

<body class="landing">

<div class="landing-container">

    <div class="landing-card">

        <div class="landing-logo">
            SP
        </div>

        <h1>Student Portal</h1>

        <p>
            Student Information Management System
        </p>

        <div class="landing-buttons">

            <a href="login.php"
               class="btn btn-primary">
                Sign In
            </a>

            <a href="signup.php"
               class="btn btn-outline">
                Create Account
            </a>

        </div>

    </div>

</div>

</body>
</html>