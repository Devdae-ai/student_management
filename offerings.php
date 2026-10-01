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

$student =
    $stmt->get_result()->fetch_assoc();

?>

<?php include "includes/header.php"; ?>

<div class="page-heading">

    <span class="eyebrow">
        ACCOUNT
    </span>

    <h1>My Profile</h1>

    <p>
        View your registered student information.
    </p>

</div>


<div class="profile-header">

    <div class="avatar profile-avatar">

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

        <h1>

            <?php

            echo htmlspecialchars(
                $student["first_name"]
                . " "
                . $student["last_name"]
            );

            ?>

        </h1>

        <p>

            Student ID:
            <?php
            echo htmlspecialchars(
                $student["student_id"]
            );
            ?>

        </p>

    </div>

    <a
        href="edit_profile.php"
        class="btn btn-primary"
    >
        Edit Profile
    </a>

</div>


<div class="panel">

    <div class="panel-header">

        <h2>Personal Information</h2>

    </div>

    <div class="profile-grid">

        <div>

            <span>First Name</span>

            <strong>
                <?php
                echo htmlspecialchars(
                    $student["first_name"]
                );
                ?>
            </strong>

        </div>


        <div>

            <span>Last Name</span>

            <strong>
                <?php
                echo htmlspecialchars(
                    $student["last_name"]
                );
                ?>
            </strong>

        </div>


        <div>

            <span>Middle Name</span>

            <strong>
                <?php
                echo htmlspecialchars(
                    $student["middle_name"]
                );
                ?>
            </strong>

        </div>


        <div>

            <span>Gender</span>

            <strong>
                <?php
                echo htmlspecialchars(
                    $student["gender"]
                );
                ?>
            </strong>

        </div>


        <div>

            <span>Date of Birth</span>

            <strong>
                <?php
                echo date(
                    "F d, Y",
                    strtotime(
                        $student["date_of_birth"]
                    )
                );
                ?>
            </strong>

        </div>


        <div>

            <span>Email Address</span>

            <strong>
                <?php
                echo htmlspecialchars(
                    $student["email"]
                );
                ?>
            </strong>

        </div>


        <div>

            <span>Contact Number</span>

            <strong>
                <?php
                echo htmlspecialchars(
                    $student["contact_number"]
                );
                ?>
            </strong>

        </div>


        <div>

            <span>Address</span>

            <strong>
                <?php
                echo htmlspecialchars(
                    $student["address"]
                );
                ?>
            </strong>

        </div>

    </div>

</div>


<div class="panel">

    <div class="panel-header">

        <h2>Academic Information</h2>

    </div>

    <div class="profile-grid">

        <div>

            <span>Course</span>

            <strong>
                <?php
                echo htmlspecialchars(
                    $student["course"]
                );
                ?>
            </strong>

        </div>


        <div>

            <span>Year Level</span>

            <strong>
                <?php
                echo htmlspecialchars(
                    $student["year_level"]
                );
                ?>
            </strong>

        </div>


        <div>

            <span>Section</span>

            <strong>
                <?php
                echo htmlspecialchars(
                    $student["section"]
                );
                ?>
            </strong>

        </div>


        <div>

            <span>Student Number</span>

            <strong>
                <?php
                echo htmlspecialchars(
                    $student["unique_student_number"]
                );
                ?>
            </strong>

        </div>

    </div>

</div>

<?php include "includes/footer.php"; ?>