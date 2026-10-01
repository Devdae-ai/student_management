<?php

require_once "config/db.php";
require_once "includes/auth.php";

$stmt = $conn->prepare(
    "SELECT *
     FROM notifications
     WHERE student_id = ?
     ORDER BY created_at DESC"
);

$stmt->bind_param(
    "i",
    $_SESSION["student_db_id"]
);

$stmt->execute();

$notifications =
    $stmt->get_result();

$conn->query(
    "UPDATE notifications
     SET is_read = 1
     WHERE student_id = " .
    intval($_SESSION["student_db_id"])
);

?>

<?php include "includes/header.php"; ?>

<div class="page-heading">

    <span class="eyebrow">
        COMMUNICATION
    </span>

    <h1>Inbox</h1>

    <p>
        Notifications and announcements from the student portal.
    </p>

</div>


<div class="panel">

    <?php if ($notifications->num_rows === 0): ?>

        <div class="empty-state">

            <div class="empty-icon">
                ✉
            </div>

            <h2>No notifications</h2>

            <p>
                You're all caught up.
            </p>

        </div>

    <?php else: ?>

        <div class="notification-list">

            <?php while ($notification = $notifications->fetch_assoc()): ?>

                <div class="notification-item">

                    <div class="notification-symbol">
                        !
                    </div>

                    <div class="notification-content">

                        <h3>
                            <?php
                            echo htmlspecialchars(
                                $notification["title"]
                            );
                            ?>
                        </h3>

                        <p>
                            <?php
                            echo htmlspecialchars(
                                $notification["message"]
                            );
                            ?>
                        </p>

                        <small>
                            <?php
                            echo date(
                                "F d, Y h:i A",
                                strtotime(
                                    $notification["created_at"]
                                )
                            );
                            ?>
                        </small>

                    </div>

                </div>

            <?php endwhile; ?>

        </div>

    <?php endif; ?>

</div>

<?php include "includes/footer.php"; ?>