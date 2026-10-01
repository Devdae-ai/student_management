<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$current_page = basename($_SERVER['PHP_SELF']);

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Portal</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
<div class="portal-layout">
    <aside class="sidebar">
        <div class="brand">
            <div class="brand-logo">SP</div>
            <div>
                <h2>Student Portal</h2>
                <small>Academic Information System</small>
            </div>
        </div>

        <nav>
            <a href="dashboard.php" class="<?php echo $current_page == 'dashboard.php' ? 'active' : ''; ?>">
                <span>⌂</span>
                Dashboard
            </a>
            <?php if (!empty($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true): ?>
            <a href="admin_dashboard.php" class="<?php echo $current_page == 'admin_dashboard.php' ? 'active' : ''; ?>">
                <span>★</span>
                Admin
            </a>
            <?php endif; ?>
            <a href="inbox.php" class="<?php echo $current_page == 'inbox.php' ? 'active' : ''; ?>">
                <span>✉</span>
                Inbox
            </a>
            <a href="offerings.php" class="<?php echo $current_page == 'offerings.php' ? 'active' : ''; ?>">
                <span>▦</span>
                Section Offering
            </a>
            <a href="profile.php" class="<?php echo $current_page == 'profile.php' ? 'active' : ''; ?>">
                <span>◉</span>
                Profile
            </a>
        </nav>

        <div class="sidebar-bottom">
            <?php if (!empty($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true): ?>
                <a href="admin_logout.php" class="logout-link">⇥ Logout</a>
            <?php else: ?>
                <a href="logout.php" class="logout-link">⇥ Logout</a>
            <?php endif; ?>
        </div>
    </aside>

    <main class="main-content">
        <header class="topbar">
            <div>
                <button class="mobile-menu" onclick="toggleSidebar()">☰</button>
            </div>
            <div class="topbar-right">
                <a href="inbox.php" class="notification-icon">🔔</a>
                <div class="user-mini">
                    <div class="avatar">
                        <?php echo strtoupper(substr($_SESSION['first_name'] ?? ($_SESSION['admin_username'] ?? 'S'), 0, 1)); ?>
                    </div>
                    <div>
                        <strong><?php echo htmlspecialchars($_SESSION['first_name'] ?? ($_SESSION['admin_username'] ?? 'User')); ?></strong>
                        <small><?php echo (!empty($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true) ? 'Admin' : 'Student'; ?></small>
                    </div>
                </div>
            </div>
        </header>
        <section class="page-content">
