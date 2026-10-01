<?php

require_once "config/db.php";
require_once "admin_auth.php";

$error = "";
$success = "";

// load colleges and programs
$colleges = [];
$programs = [];
$c_stmt = $conn->prepare("SELECT id, name FROM colleges ORDER BY name");
if ($c_stmt) { $c_stmt->execute(); $c_res = $c_stmt->get_result(); while ($r = $c_res->fetch_assoc()) $colleges[] = $r; $c_stmt->close(); }
$p_stmt = $conn->prepare("SELECT id, college_id, name FROM programs ORDER BY name");
if ($p_stmt) { $p_stmt->execute(); $p_res = $p_stmt->get_result(); while ($r = $p_res->fetch_assoc()) $programs[] = $r; $p_stmt->close(); }

if (
    $_SERVER["REQUEST_METHOD"] === "POST"
) {
    $student_id = trim($_POST["student_id"] ?? "");
    $first_name = trim($_POST["first_name"] ?? "");
    $last_name = trim($_POST["last_name"] ?? "");
    $middle_name = trim($_POST["middle_name"] ?? "");
    $gender = $_POST["gender"] ?? "";
    $dob = $_POST["date_of_birth"] ?? null;
    $email = trim($_POST["email"] ?? "");
    $address = trim($_POST["address"] ?? "");
    $contact = trim($_POST["contact_number"] ?? "");
    $college = trim($_POST["college"] ?? "");
    $course = trim($_POST["course"] ?? "");
    $year_level = trim($_POST["year_level"] ?? "");
    $password = $_POST["password"] ?? "";

    if (empty($first_name) || empty($last_name) || empty($email) || empty($course) || empty($year_level) || empty($password)) {
        $error = "Please complete required fields.";
    } else {
        // ensure unique student_id if provided
        if ($student_id !== "") {
            $chk = $conn->prepare("SELECT id FROM students WHERE student_id = ?");
            $chk->bind_param("s", $student_id);
            $chk->execute();
            $r = $chk->get_result();
            if ($r->num_rows > 0) {
                $error = "Student ID already exists.";
            }
            $chk->close();
        }

        // ensure email unique
        $chk2 = $conn->prepare("SELECT id FROM students WHERE email = ?");
        $chk2->bind_param("s", $email);
        $chk2->execute();
        $r2 = $chk2->get_result();
        if ($r2->num_rows > 0) {
            $error = "Email already registered.";
        }
        $chk2->close();

        if ($error === "") {
            // generate student_id if empty
            if ($student_id === "") $student_id = 'S' . strtoupper(substr(uniqid(), -8));

            // generate unique student number
            do {
                $part1 = random_int(10,99);
                $part2 = random_int(1000,9999);
                $part3 = random_int(100,999);
                $unique_number = "$part1-$part2-$part3";
                $c = $conn->prepare("SELECT id FROM students WHERE unique_student_number = ?");
                $c->bind_param("s", $unique_number);
                $c->execute();
                $cr = $c->get_result();
            } while ($cr->num_rows > 0);

            $hash = password_hash($password, PASSWORD_DEFAULT);
            $section = "Pending";

            $stmt = $conn->prepare("INSERT INTO students (student_id, first_name, last_name, middle_name, gender, date_of_birth, email, address, contact_number, college, course, year_level, section, unique_student_number, password) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("sssssssssssssss", $student_id, $first_name, $last_name, $middle_name, $gender, $dob, $email, $address, $contact, $college, $course, $year_level, $section, $unique_number, $hash);
            if ($stmt->execute()) {
                $success = "Student added successfully.";
            } else {
                $error = "Failed to add student.";
            }
            $stmt->close();
        }
    }
}

?>

<?php include "includes/header.php"; ?>

<div class="page-heading">
    <span class="eyebrow">ADMIN</span>
    <h1>Add Student</h1>
    <p>Fill in student details.</p>
</div>

<?php if ($success): ?><div class="alert success"><?php echo htmlspecialchars($success); ?></div><?php endif; ?>
<?php if ($error): ?><div class="alert error"><?php echo htmlspecialchars($error); ?></div><?php endif; ?>

<div class="panel">
<form method="POST">
    <div class="form-grid">
        <div class="form-group">
            <label>Student ID (optional)</label>
            <input type="text" name="student_id">
        </div>
        <div class="form-group">
            <label>First Name</label>
            <input type="text" name="first_name" required>
        </div>
        <div class="form-group">
            <label>Last Name</label>
            <input type="text" name="last_name" required>
        </div>
        <div class="form-group">
            <label>Middle Name</label>
            <input type="text" name="middle_name">
        </div>
        <div class="form-group">
            <label>Gender</label>
            <select name="gender"><option value="Male">Male</option><option value="Female">Female</option><option value="Prefer not to say">Prefer not to say</option></select>
        </div>
        <div class="form-group">
            <label>Date of Birth</label>
            <input type="date" name="date_of_birth">
        </div>
        <div class="form-group">
            <label>Email</label>
            <input type="email" name="email" required>
        </div>
        <div class="form-group">
            <label>Contact Number</label>
            <input type="text" name="contact_number">
        </div>
        <div class="form-group">
            <label>Department</label>
            <select name="college">
                <option value="">Select</option>
                <?php foreach($colleges as $c): ?>
                    <option value="<?php echo htmlspecialchars($c['name']); ?>"><?php echo htmlspecialchars($c['name']); ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="form-group">
            <label>Program / Course</label>
            <select name="course">
                <option value="">Select</option>
                <?php foreach($programs as $p): ?>
                    <option value="<?php echo htmlspecialchars($p['name']); ?>"><?php echo htmlspecialchars($p['name']); ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="form-group">
            <label>Year Level</label>
            <select name="year_level"><option value="1st Year">1st Year</option><option value="2nd Year">2nd Year</option><option value="3rd Year">3rd Year</option><option value="4th Year">4th Year</option></select>
        </div>
        <div class="form-group">
            <label>Password</label>
            <input type="password" name="password" required>
        </div>
    </div>

    <div class="form-actions">
        <a href="admin_dashboard.php" class="btn btn-outline">Cancel</a>
        <button type="submit" class="btn btn-primary">Add Student</button>
    </div>
</form>
</div>

<?php include "includes/footer.php"; ?>
