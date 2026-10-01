<?php

require_once "config/db.php";

session_start();

if (isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true) {
    header("Location: admin_dashboard.php");
    exit();
}

$error = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $username = trim($_POST["username"] ?? "");
    $password = trim($_POST["password"] ?? "");

    // Authenticate against the `admins` table using password_verify
    $stmt = $conn->prepare("SELECT password FROM admins WHERE username = ?");
    if ($stmt) {
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $stmt->store_result();
        if ($stmt->num_rows === 1) {
            $stmt->bind_result($hash);
            $stmt->fetch();
            if (password_verify($password, $hash)) {
                $_SESSION["admin_logged_in"] = true;
                $_SESSION["admin_username"] = $username;
                $stmt->close();
                header("Location: admin_dashboard.php");
                exit();
            }
        }
        $stmt->close();
    }

    $error = "Invalid admin credentials.";
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>

<div class="auth-page">
    <div class="auth-container login-box">
        <div class="auth-header">
            <div class="landing-logo">SP</div>
            <h1>Admin Portal</h1>
            <p>Manage student profiles and inbox messages.</p>
        </div>

        <?php if (!empty($error)): ?>
            <div class="alert error"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>

        <form method="POST">
            <div class="form-group">
                <label>Username</label>
                <input type="text" name="username" placeholder="Enter admin username" required>
            </div>

            <div class="form-group">
                <label>Password</label>
                <input type="password" name="password" placeholder="Enter admin password" required>
            </div>

            <button type="submit" class="btn btn-primary full-button">Admin Sign In</button>
        </form>

        <div class="auth-footer">
            Demo admin login:<br>
            Username: admin<br>
            Password: admin123
        </div>
    </div>
</div>

</body>
</html>
