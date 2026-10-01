<?php

require_once "config/db.php";
require_once "includes/auth.php";

$stmt = $conn->prepare(
    "SELECT *
     FROM students
     WHERE id = ?"
);

$stmt->bind_param(
    "i",
    $_SESSION["student_db_id"]
);

$stmt->execute();

$student = $stmt->get_result()->fetch_assoc();


$notification_stmt = $conn->prepare(
    "SELECT COUNT(*) AS total
     FROM notifications
     WHERE student_id = ?
     AND is_read = 0"
);

$notification_stmt->bind_param(
    "i",
    $_SESSION["student_db_id"]
);

$notification_stmt->execute();

$notification_count =
    $notification_stmt
        ->get_result()
        ->fetch_assoc()["total"];

?>

<?php include "includes/header.php"; ?>

<div class="welcome-section">

    <div>

        <span class="eyebrow">
            STUDENT PORTAL
        </span>

        <h1>
            Welcome,
            <?php echo htmlspecialchars(
                $student["first_name"]
            ); ?>!
        </h1>

        <p>
            Here's an overview of your student information.
        </p>

    </div>

    <a href="profile.php"
       class="btn btn-outline">
        View Profile
    </a>

</div>


<div class="dashboard-grid">

    <div class="dashboard-card">

        <div class="card-icon">
            ◉
        </div>

        <div>

            <span>
                Student Number
            </span>

            <h2>
                <?php
                echo htmlspecialchars(
                    $student["unique_student_number"]
                );
                ?>
            </h2>

        </div>

    </div>


    <div class="dashboard-card">

        <div class="card-icon">
            ▣
        </div>

        <div>

            <span>
                Course
            </span>

            <h2>
                <?php
                echo htmlspecialchars(
                    $student["course"]
                );
                ?>
            </h2>

        </div>

    </div>


    <div class="dashboard-card">

        <div class="card-icon">
            #
        </div>

        <div>

            <span>
                Year Level
            </span>

            <h2>
                <?php
                echo htmlspecialchars(
                    $student["year_level"]
                );
                ?>
            </h2>

        </div>

    </div>


    <div class="dashboard-card">

        <div class="card-icon">
            ✉
        </div>

        <div>

            <span>
                Unread Notifications
            </span>

            <h2>
                <?php echo $notification_count; ?>
            </h2>

        </div>

    </div>

</div>


<div class="content-grid">

    <div class="panel">

        <div class="panel-header">

            <h2>Student Information</h2>

            <a href="profile.php">
                View Profile
            </a>

        </div>

        <div class="student-summary">

            <div class="avatar large">

                <?php
                echo strtoupper(
                    substr(
                        $student["first_name"],
                        0,
                        1
                    )
                );
                ?>

            </div>

            <div>

                <h2>
                    <?php
                    echo htmlspecialchars(
                        $student["first_name"]
                        . " "
                        . $student["last_name"]
                    );
                    ?>
                </h2>

                <p>
                    Student ID:
                    <?php
                    echo htmlspecialchars(
                        $student["student_id"]
                    );
                    ?>
                </p>

            </div>

        </div>

    </div>


    <div class="panel">

        <div class="panel-header">

            <h2>Quick Actions</h2>

        </div>

        <div class="quick-actions">

            <a href="inbox.php">
                <span>✉</span>
                <strong>Open Inbox</strong>
                <small>
                    View notifications
                </small>
            </a>

            <a href="offerings.php">
                <span>▦</span>
                <strong>Section Offering</strong>
                <small>
                    View section information
                </small>
            </a>

            <a href="edit_profile.php">
                <span>✎</span>
                <strong>Edit Profile</strong>
                <small>
                    Update your information
                </small>
            </a>

        </div>

    </div>

</div>

<?php include "includes/footer.php"; ?>