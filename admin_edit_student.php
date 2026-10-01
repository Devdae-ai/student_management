<?php
require_once "config/db.php";
require_once "admin_auth.php";

$id = (int) ($_GET['id'] ?? 0);
if ($id <= 0) { header('Location: admin_dashboard.php'); exit(); }

$error = '';
$success = '';

$stmt = $conn->prepare("SELECT * FROM students WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$student = $stmt->get_result()->fetch_assoc();
$stmt->close();
if (!$student) { header('Location: admin_dashboard.php'); exit(); }

// load colleges/programs
$colleges = [];
$programs = [];
$c_stmt = $conn->prepare("SELECT id, name FROM colleges ORDER BY name"); if ($c_stmt) { $c_stmt->execute(); $c_res = $c_stmt->get_result(); while ($r = $c_res->fetch_assoc()) $colleges[] = $r; $c_stmt->close(); }
$p_stmt = $conn->prepare("SELECT id, college_id, name FROM programs ORDER BY name"); if ($p_stmt) { $p_stmt->execute(); $p_res = $p_stmt->get_result(); while ($r = $p_res->fetch_assoc()) $programs[] = $r; $p_stmt->close(); }

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $first_name = trim($_POST['first_name'] ?? '');
    $last_name = trim($_POST['last_name'] ?? '');
    $middle_name = trim($_POST['middle_name'] ?? '');
    $gender = $_POST['gender'] ?? '';
    $dob = $_POST['date_of_birth'] ?? null;
    $email = trim($_POST['email'] ?? '');
    $address = trim($_POST['address'] ?? '');
    $contact = trim($_POST['contact_number'] ?? '');
    $college = trim($_POST['college'] ?? '');
    $course = trim($_POST['course'] ?? '');
    $year_level = trim($_POST['year_level'] ?? '');

    if (empty($first_name) || empty($last_name) || empty($email)) {
        $error = 'Please complete required fields.';
    } else {
        $update = $conn->prepare("UPDATE students SET first_name=?, last_name=?, middle_name=?, gender=?, date_of_birth=?, email=?, address=?, contact_number=?, college=?, course=?, year_level=? WHERE id=?");
        $update->bind_param('sssssssssssi', $first_name, $last_name, $middle_name, $gender, $dob, $email, $address, $contact, $college, $course, $year_level, $id);
        if ($update->execute()) { $success = 'Student updated.'; }
        else { $error = 'Update failed.'; }
        $update->close();
    }
}

?>
<?php include 'includes/header.php'; ?>
<div class="page-heading"><span class="eyebrow">ADMIN</span><h1>Edit Student</h1><p>Modify student record.</p></div>
<?php if ($success): ?><div class="alert success"><?php echo htmlspecialchars($success); ?></div><?php endif; ?>
<?php if ($error): ?><div class="alert error"><?php echo htmlspecialchars($error); ?></div><?php endif; ?>
<div class="panel">
<form method="POST">
    <div class="form-grid">
        <div class="form-group"><label>Student ID</label><input type="text" name="student_id" value="<?php echo htmlspecialchars($student['student_id']); ?>" disabled></div>
        <div class="form-group"><label>First Name</label><input type="text" name="first_name" value="<?php echo htmlspecialchars($student['first_name']); ?>" required></div>
        <div class="form-group"><label>Last Name</label><input type="text" name="last_name" value="<?php echo htmlspecialchars($student['last_name']); ?>" required></div>
        <div class="form-group"><label>Middle Name</label><input type="text" name="middle_name" value="<?php echo htmlspecialchars($student['middle_name']); ?>"></div>
        <div class="form-group"><label>Gender</label><select name="gender"><option value="Male" <?php echo $student['gender']=='Male'?'selected':''; ?>>Male</option><option value="Female" <?php echo $student['gender']=='Female'?'selected':''; ?>>Female</option><option value="Prefer not to say" <?php echo $student['gender']=='Prefer not to say'?'selected':''; ?>>Prefer not to say</option></select></div>
        <div class="form-group"><label>Date of Birth</label><input type="date" name="date_of_birth" value="<?php echo htmlspecialchars($student['date_of_birth']); ?>"></div>
        <div class="form-group"><label>Email</label><input type="email" name="email" value="<?php echo htmlspecialchars($student['email']); ?>" required></div>
        <div class="form-group"><label>Contact Number</label><input type="text" name="contact_number" value="<?php echo htmlspecialchars($student['contact_number']); ?>"></div>
        <div class="form-group"><label>Department</label><select name="college"><option value="">Select</option><?php foreach($colleges as $c): ?><option value="<?php echo htmlspecialchars($c['name']); ?>" <?php echo $student['college']==$c['name']?'selected':''; ?>><?php echo htmlspecialchars($c['name']); ?></option><?php endforeach; ?></select></div>
        <div class="form-group"><label>Program</label><select name="course"><option value="">Select</option><?php foreach($programs as $p): ?><option value="<?php echo htmlspecialchars($p['name']); ?>" <?php echo $student['course']==$p['name']?'selected':''; ?>><?php echo htmlspecialchars($p['name']); ?></option><?php endforeach; ?></select></div>
        <div class="form-group"><label>Year Level</label><select name="year_level"><option value="1st Year" <?php echo $student['year_level']=='1st Year'?'selected':''; ?>>1st Year</option><option value="2nd Year" <?php echo $student['year_level']=='2nd Year'?'selected':''; ?>>2nd Year</option><option value="3rd Year" <?php echo $student['year_level']=='3rd Year'?'selected':''; ?>>3rd Year</option><option value="4th Year" <?php echo $student['year_level']=='4th Year'?'selected':''; ?>>4th Year</option></select></div>
    </div>
    <div class="form-actions"><a href="admin_dashboard.php" class="btn btn-outline">Cancel</a><button type="submit" class="btn btn-primary">Save Changes</button></div>
</form>
</div>
<?php include 'includes/footer.php'; ?>
