<?php

require_once "config/db.php";

session_start();

if (isset($_SESSION['student_db_id'])) {
    header("Location: dashboard.php");
    exit();
}

$error = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    // Use the unique student number for login (guard against undefined index)
    $student_number = trim($_POST["student_number"] ?? "");
    $password = $_POST["password"];

    $stmt = $conn->prepare(
        "SELECT *
         FROM students
         WHERE unique_student_number = ?"
    );

    $stmt->bind_param(
        "s",
        $student_number
    );

    $stmt->execute();

    $result = $stmt->get_result();

    if ($result->num_rows === 1) {

        $student = $result->fetch_assoc();

        if (
            password_verify(
                $password,
                $student["password"]
            )
        ) {


            $_SESSION["student_db_id"] = $student["id"];
            // keep original student_id for compatibility
            $_SESSION["student_id"] = $student["student_id"];
            // primary login key: unique student number
            $_SESSION["unique_student_number"] = $student["unique_student_number"];
            $_SESSION["first_name"] = $student["first_name"];
            $_SESSION["last_name"] = $student["last_name"];

            header("Location: dashboard.php");
            exit();

        } else {

            $error = "Incorrect password.";

        }

    } else {

        $error = "Student number not found.";

    }

}

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <title>Student Login</title>

    <link rel="stylesheet"
          href="assets/css/style.css">

</head>

<body>

<div class="auth-page">

    <div class="auth-container login-box">

        <div class="auth-header">

            <div class="landing-logo">
                SP
            </div>

            <h1>Welcome Back</h1>

            <p>
                Sign in to your student portal.
            </p>

        </div>

        <?php if (isset($_GET["registered"])): ?>

            <div class="alert success">

                Account successfully created.
                You may now sign in.

            </div>

        <?php endif; ?>

        <?php if (!empty($error)): ?>

            <div class="alert error">

                <?php echo htmlspecialchars($error); ?>

            </div>

        <?php endif; ?>

        <form method="POST">

            <div class="form-group">

                <label>Student Number</label>

                    <input
                        type="text"
                        name="student_number"
                        placeholder="Enter Student Number"
                        required
                    >

            </div>

            <div class="form-group">

                <label>Password</label>

                <input
                    type="password"
                    name="password"
                    placeholder="Enter password"
                    required
                >

            </div>

            <button
                type="submit"
                class="btn btn-primary full-button"
            >
                Sign In
            </button>

        </form>

        <div class="auth-footer">

            Don't have an account?

            <a href="signup.php">
                Create one
            </a>

        </div>

    </div>

</div>

</body>
</html>