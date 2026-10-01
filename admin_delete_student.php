<?php
require_once "config/db.php";
require_once "admin_auth.php";

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: admin_dashboard.php');
    exit();
}

$id = (int) ($_POST['id'] ?? 0);
if ($id <= 0) { header('Location: admin_dashboard.php'); exit(); }

$stmt = $conn->prepare("DELETE FROM students WHERE id = ?");
$stmt->bind_param('i', $id);
if ($stmt->execute()) {
    $stmt->close();
    header('Location: admin_dashboard.php');
    exit();
} else {
    $stmt->close();
    // on failure, still redirect with no details
    header('Location: admin_dashboard.php');
    exit();
}
?>