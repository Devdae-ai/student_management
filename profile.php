<?php

require_once "config/db.php";
require_once "includes/auth.php";

$stmt = $conn->prepare("SELECT * FROM students WHERE id = ?");
$stmt->bind_param("i", $_SESSION["student_db_id"]);
$stmt->execute();
$student = $stmt->get_result()->fetch_assoc();

if (!$student) {
    header("Location: logout.php");
    exit();
}

include "includes/header.php";
?>

<div class="panel">
    <div class="panel-header">
        <h2>My Profile</h2>
        <a href="edit_profile.php">Edit Profile</a>
    </div>

    <div class="student-summary">
        <div class="avatar large">
            <?php echo strtoupper(substr($student['first_name'], 0, 1)); ?>
        </div>
        <div>
            <h2><?php echo htmlspecialchars($student['first_name'] . ' ' . $student['last_name']); ?></h2>
            <p>Student ID: <?php echo htmlspecialchars($student['student_id']); ?></p>
        </div>
    </div>

    <div class="dashboard-grid" style="margin-top: 24px;">
        <div class="dashboard-card">
            <span>Full Name</span>
            <h2><?php echo htmlspecialchars($student['first_name'] . ' ' . $student['middle_name'] . ' ' . $student['last_name']); ?></h2>
        </div>
        <div class="dashboard-card">
            <span>Email</span>
            <h2><?php echo htmlspecialchars($student['email']); ?></h2>
        </div>
        <div class="dashboard-card">
            <span>Course</span>
            <h2><?php echo htmlspecialchars($student['course']); ?></h2>
        </div>
        <div class="dashboard-card">
            <span>Year Level</span>
            <h2><?php echo htmlspecialchars($student['year_level']); ?></h2>
        </div>
    </div>
</div>

<?php include "includes/footer.php"; ?>
