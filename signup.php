<?php

require_once "config/db.php";

session_start();

$error = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $first_name = trim($_POST["first_name"]);
    $last_name = trim($_POST["last_name"]);
    $middle_name = trim($_POST["middle_name"]);

    $gender = $_POST["gender"];
    $date_of_birth = $_POST["date_of_birth"];

    $email = trim($_POST["email"]);
    $address = trim($_POST["address"]);
    $contact_number = trim($_POST["contact_number"]);

    $college = trim($_POST["college"] ?? "");
    $course = trim($_POST["course"]);
    $year_level = trim($_POST["year_level"]);

    $password = $_POST["password"];
    $confirm_password = $_POST["confirm_password"];

    if (
        empty($first_name) ||
        empty($last_name) ||
        empty($gender) ||
        empty($date_of_birth) ||
        empty($email) ||
        empty($address) ||
        empty($contact_number) ||
        empty($college) ||
        empty($course) ||
        empty($year_level) ||
        empty($password)
    ) {

        $error = "Please complete all required fields.";

    } elseif ($password !== $confirm_password) {

        $error = "Passwords do not match.";

    } else {

            $check = $conn->prepare(
            "SELECT id FROM students WHERE email = ?"
        );

        $check->bind_param("s", $email);
        $check->execute();
        $result = $check->get_result();

        if ($result->num_rows > 0) {

            $error = "Email address is already registered.";

        } else {

            /*
             * Example unique student number:
             *
             * 14-0065-476
             *
             * The system generates a 9-digit identifier
             * displayed in the requested format.
             */

            do {

                $part1 = random_int(10, 99);
                $part2 = random_int(1000, 9999);
                $part3 = random_int(100, 999);

                $unique_number =
                    $part1 . "-" .
                    $part2 . "-" .
                    $part3;

                $check_number = $conn->prepare(
                    "SELECT id FROM students
                     WHERE unique_student_number = ?"
                );

                $check_number->bind_param(
                    "s",
                    $unique_number
                );

                $check_number->execute();

                $number_result =
                    $check_number->get_result();


            } while ($number_result->num_rows > 0);

            // Generate a server-side student_id to avoid asking users for it
            $student_id = 'S' . strtoupper(substr(uniqid(), -8));

            // Ensure generated student_id is unique
            $check_sid = $conn->prepare("SELECT id FROM students WHERE student_id = ?");
            $check_sid->bind_param("s", $student_id);
            $check_sid->execute();
            $sid_result = $check_sid->get_result();
            while ($sid_result->num_rows > 0) {
                $student_id = 'S' . strtoupper(substr(uniqid(), -8));
                $check_sid->bind_param("s", $student_id);
                $check_sid->execute();
                $sid_result = $check_sid->get_result();
            }
            $check_sid->close();

            $hashed_password =
                password_hash(
                    $password,
                    PASSWORD_DEFAULT
                );

            $section = "Pending";

            $stmt = $conn->prepare(
                "INSERT INTO students
                (
                    student_id,
                    first_name,
                    last_name,
                    middle_name,
                    gender,
                    date_of_birth,
                    email,
                    address,
                    contact_number,
                    college,
                    course,
                    year_level,
                    section,
                    unique_student_number,
                    password
                )
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)"
            );

            $stmt->bind_param(
                "sssssssssssssss",
                $student_id,
                $first_name,
                $last_name,
                $middle_name,
                $gender,
                $date_of_birth,
                $email,
                $address,
                $contact_number,
                $college,
                $course,
                $year_level,
                $section,
                $unique_number,
                $hashed_password
            );

            if ($stmt->execute()) {

                $new_student_id = $conn->insert_id;

                $notification_title = "Welcome to the Student Portal";
                $notification_message = "Your student account has been successfully created.";

                $notification = $conn->prepare(
                    "INSERT INTO notifications (student_id, title, message) VALUES (?, ?, ?)"
                );

                $notification->bind_param("iss", $new_student_id, $notification_title, $notification_message);
                $notification->execute();

                // Log the user in automatically
                $_SESSION["student_db_id"] = $new_student_id;
                $_SESSION["student_id"] = $student_id;
                $_SESSION["unique_student_number"] = $unique_number;
                $_SESSION["first_name"] = $first_name;
                $_SESSION["last_name"] = $last_name;

                header("Location: dashboard.php");
                exit();

            } else {

                $error = "Registration failed. Please try again.";

            }

        }

    }

}
// Fetch colleges and programs for the department -> program selector
$colleges = [];
$programs = [];
try {
    $c_stmt = $conn->prepare("SELECT id, name FROM colleges ORDER BY name");
    if ($c_stmt) {
        $c_stmt->execute();
        $c_res = $c_stmt->get_result();
        while ($row = $c_res->fetch_assoc()) {
            $colleges[] = $row;
        }
        $c_stmt->close();
    }

    $p_stmt = $conn->prepare("SELECT id, college_id, name FROM programs ORDER BY name");
    if ($p_stmt) {
        $p_stmt->execute();
        $p_res = $p_stmt->get_result();
        while ($row = $p_res->fetch_assoc()) {
            $programs[] = $row;
        }
        $p_stmt->close();
    }
} catch (Exception $e) {
    // ignore - page will still work with static options
}

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <title>Create Student Account</title>

    <link rel="stylesheet"
          href="assets/css/style.css">

</head>

<body>

<div class="auth-page">

    <div class="auth-container wide">

        <div class="auth-header">

            <div class="landing-logo">
                SP
            </div>

            <h1>Create Student Account</h1>

            <p>
                Enter your student information to create
                your portal account.
            </p>

        </div>

        <?php if (!empty($error)): ?>

            <div class="alert error">
                <?php echo htmlspecialchars($error); ?>
            </div>

        <?php endif; ?>

        <form method="POST">

            <h3>Personal Information</h3>

            <div class="form-grid">

                <!-- Student ID is generated server-side; field removed -->

                <div class="form-group">

                    <label>First Name *</label>

                    <input
                        type="text"
                        name="first_name"
                        required
                    >

                </div>

                <div class="form-group">

                    <label>Last Name *</label>

                    <input
                        type="text"
                        name="last_name"
                        required
                    >

                </div>

                <div class="form-group">

                    <label>Middle Name</label>

                    <input
                        type="text"
                        name="middle_name"
                    >

                </div>

                <div class="form-group">

                    <label>Gender *</label>

                    <select name="gender" required>

                        <option value="">
                            Select Gender
                        </option>

                        <option value="Male">
                            Male
                        </option>

                        <option value="Female">
                            Female
                        </option>

                        <option value="Prefer not to say">
                            Prefer not to say
                        </option>

                    </select>

                </div>

                <div class="form-group">

                    <label>Date of Birth *</label>

                    <input
                        type="date"
                        name="date_of_birth"
                        required
                    >

                </div>

                <div class="form-group">

                    <label>Email Address *</label>

                    <input
                        type="email"
                        name="email"
                        required
                    >

                </div>

                <div class="form-group">

                    <label>Contact Number *</label>

                    <input
                        type="text"
                        name="contact_number"
                        required
                    >

                </div>

                <div class="form-group full">

                    <label>Address *</label>

                    <textarea
                        name="address"
                        required
                    ></textarea>

                </div>

            </div>

            <h3>Academic Information</h3>

            <div class="form-grid">

                <div class="form-group">

                    <label>Department *</label>

                    <select name="college" id="college-select" required>
                        <option value="">Select Department</option>
                        <?php foreach ($colleges as $c): ?>
                            <option value="<?php echo htmlspecialchars($c['name']); ?>"><?php echo htmlspecialchars($c['name']); ?></option>
                        <?php endforeach; ?>
                    </select>

                </div>

                <div class="form-group">

                    <label>Program / Course *</label>

                    <select name="course" id="program-select" required>
                        <option value="">Select Program</option>
                        <?php foreach ($programs as $p): ?>
                            <option data-college-id="<?php echo htmlspecialchars($p['college_id']); ?>" value="<?php echo htmlspecialchars($p['name']); ?>"><?php echo htmlspecialchars($p['name']); ?></option>
                        <?php endforeach; ?>
                    </select>

                </div>

                <div class="form-group">

                    <label>Year Level *</label>

                    <select name="year_level" required>

                        <option value="">
                            Select Year Level
                        </option>

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

            <h3>Account Security</h3>

            <div class="form-grid">

                <div class="form-group">

                    <label>Password *</label>

                    <input
                        type="password"
                        name="password"
                        required
                    >

                </div>

                <div class="form-group">

                    <label>Confirm Password *</label>

                    <input
                        type="password"
                        name="confirm_password"
                        required
                    >

                </div>

            </div>

            <button
                type="submit"
                class="btn btn-primary full-button"
            >
                Create Student Account
            </button>

        </form>

<script>
// Filter program options based on selected college
(function(){
    const collegeSelect = document.getElementById('college-select');
    const programSelect = document.getElementById('program-select');

    if (!collegeSelect || !programSelect) return;

    function filterPrograms() {
        const selected = collegeSelect.value;
        const options = programSelect.querySelectorAll('option[data-college-id]');
        options.forEach(opt => {
            const collegeId = opt.getAttribute('data-college-id');
            // find college name for this id by looking up option in collegeSelect
            const collegeOption = Array.from(collegeSelect.options).find(o => o.value && o.value.trim() !== '' && o.textContent.trim() === collegeSelect.value);
            // If no selected college, show all
            if (!selected) {
                opt.style.display = '';
                return;
            }
            // Show option if its college id matches the currently selected college's index mapping
            // We stored college name in collegeSelect value, so match by name using data attribute mapping built below
            // Simple approach: show all options whose parent college name matches selected via lookup table generated from server
        });
    }

    // Build mapping from college id to name from server-side PHP data
    const collegeMap = {};
    <?php foreach ($colleges as $c): ?>
        collegeMap[<?php echo (int)$c['id']; ?>] = <?php echo json_encode($c['name']); ?>;
    <?php endforeach; ?>

    function applyFilter() {
        const selectedName = collegeSelect.value;
        Array.from(programSelect.options).forEach(opt => {
            const cid = opt.getAttribute('data-college-id');
            if (!cid) { opt.style.display = ''; return; }
            const name = collegeMap[cid];
            opt.style.display = (selectedName === '' || name === selectedName) ? '' : 'none';
        });
        // Reset program select value if currently hidden
        if (programSelect.value) {
            const currentOpt = programSelect.options[programSelect.selectedIndex];
            if (currentOpt && currentOpt.style.display === 'none') {
                programSelect.value = '';
            }
        }
    }

    collegeSelect.addEventListener('change', applyFilter);
    // initial filter
    applyFilter();
})();
</script>

        <div class="auth-footer">

            Already have an account?

            <a href="login.php">
                Sign in
            </a>

        </div>

    </div>

</div>

</body>
</html>