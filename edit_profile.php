<?php

require_once "config/db.php";
require_once "includes/auth.php";

$id = $_SESSION["student_db_id"];

$error = "";
$success = "";

$stmt = $conn->prepare(
    "SELECT *
     FROM students
     WHERE id = ?"
);

$stmt->bind_param("i", $id);
$stmt->execute();

$student =
    $stmt->get_result()->fetch_assoc();


if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $first_name =
        trim($_POST["first_name"]);

    $last_name =
        trim($_POST["last_name"]);

    $middle_name =
        trim($_POST["middle_name"]);

    $gender =
        $_POST["gender"];

    $date_of_birth =
        $_POST["date_of_birth"];

    $email =
        trim($_POST["email"]);

    $address =
        trim($_POST["address"]);

    $contact_number =
        trim($_POST["contact_number"]);

    $course =
        trim($_POST["course"]);

    $year_level =
        trim($_POST["year_level"]);


    $update = $conn->prepare(
        "UPDATE students SET
            first_name = ?,
            last_name = ?,
            middle_name = ?,
            gender = ?,
            date_of_birth = ?,
            email = ?,
            address = ?,
            contact_number = ?,
            course = ?,
            year_level = ?
         WHERE id = ?"
    );

    $update->bind_param(
        "ssssssssssi",
        $first_name,
        $last_name,
        $middle_name,
        $gender,
        $date_of_birth,
        $email,
        $address,
        $contact_number,
        $course,
        $year_level,
        $id
    );

    if ($update->execute()) {

        $_SESSION["first_name"] =
            $first_name;

        $success =
            "Profile updated successfully.";

        $stmt->execute();

        $student =
            $stmt->get_result()->fetch_assoc();

    } else {

        $error =
            "Unable to update profile.";

    }

}

?>

<?php include "includes/header.php"; ?>

<div class="page-heading">

    <span class="eyebrow">
        ACCOUNT SETTINGS
    </span>

    <h1>Edit Profile</h1>

    <p>
        Update your personal and academic information.
    </p>

</div>


<?php if ($success): ?>

    <div class="alert success">

        <?php echo htmlspecialchars($success); ?>

    </div>

<?php endif; ?>


<?php if ($error): ?>

    <div class="alert error">

        <?php echo htmlspecialchars($error); ?>

    </div>

<?php endif; ?>


<div class="panel">

<form method="POST">

    <h3>Personal Information</h3>

    <div class="form-grid">

        <div class="form-group">

            <label>First Name</label>

            <input
                type="text"
                name="first_name"
                value="<?php
                    echo htmlspecialchars(
                        $student["first_name"]
                    );
                ?>"
                required
            >

        </div>


        <div class="form-group">

            <label>Last Name</label>

            <input
                type="text"
                name="last_name"
                value="<?php
                    echo htmlspecialchars(
                        $student["last_name"]
                    );
                ?>"
                required
            >

        </div>


        <div class="form-group">

            <label>Middle Name</label>

            <input
                type="text"
                name="middle_name"
                value="<?php
                    echo htmlspecialchars(
                        $student["middle_name"]
                    );
                ?>"
            >

        </div>


        <div class="form-group">

            <label>Gender</label>

            <select name="gender">

                <option value="Male"
                    <?php
                    echo $student["gender"] == "Male"
                        ? "selected"
                        : "";
                    ?>>
                    Male
                </option>

                <option value="Female"
                    <?php
                    echo $student["gender"] == "Female"
                        ? "selected"
                        : "";
                    ?>>
                    Female
                </option>

                <option value="Prefer not to say"
                    <?php
                    echo $student["gender"] == "Prefer not to say"
                        ? "selected"
                        : "";
                    ?>>
                    Prefer not to say
                </option>

            </select>

        </div>


        <div class="form-group">

            <label>Date of Birth</label>

            <input
                type="date"
                name="date_of_birth"
                value="<?php
                    echo htmlspecialchars(
                        $student["date_of_birth"]
                    );
                ?>"
                required
            >

        </div>


        <div class="form-group">

            <label>Email Address</label>

            <input
                type="email"
                name="email"
                value="<?php
                    echo htmlspecialchars(
                        $student["email"]
                    );
                ?>"
                required
            >

        </div>


        <div class="form-group">

            <label>Contact Number</label>

            <input
                type="text"
                name="contact_number"
                value="<?php
                    echo htmlspecialchars(
                        $student["contact_number"]
                    );
                ?>"
                required
            >

        </div>


        <div class="form-group full">

            <label>Address</label>

            <textarea
                name="address"
                required
            ><?php
                echo htmlspecialchars(
                    $student["address"]
                );
            ?></textarea>

        </div>

    </div>


    <h3>Academic Information</h3>

    <div class="form-grid">

        <div class="form-group">

            <label>Course</label>

            <select name="course">

                <option value="BS Information Technology">
                    BS Information Technology
                </option>

                <option value="BS Computer Science">
                    BS Computer Science
                </option>

                <option value="BS Information Systems">
                    BS Information Systems
                </option>

                <option value="BS Business Administration">
                    BS Business Administration
                </option>

            </select>

        </div>


        <div class="form-group">

            <label>Year Level</label>

            <select name="year_level">

                <option value="1st Year">
                    1st Year
                </option>

                <option value="2nd Year">
                    2nd Year
                </option>

                <option value="3rd Year">
                    3rd Year
                </option>

                <option value="4th Year">
                    4th Year
                </option>

            </select>

        </div>

    </div>


    <div class="form-actions">

        <a
            href="profile.php"
            class="btn btn-outline"
        >
            Cancel
        </a>

        <button
            type="submit"
            class="btn btn-primary"
        >
            Save Changes
        </button>

    </div>

</form>

</div>

<?php include "includes/footer.php"; ?>