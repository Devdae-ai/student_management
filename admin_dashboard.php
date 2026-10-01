asas<?php

require_once "config/db.php";
require_once "admin_auth.php";

$error = "";
$success = "";

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["send_message"])) {
    $student_id = (int) ($_POST["student_id"] ?? 0);
    $title = trim($_POST["title"] ?? "");
    <?php include "includes/header.php"; ?>

    <div class="page-heading">
        <span class="eyebrow">ADMIN</span>
        <h1>Admin Dashboard</h1>
        <p>Manage students and send messages to their inbox.</p>
    </div>

    <?php if (!empty($success)): ?>
        <div class="alert success"><?php echo htmlspecialchars($success); ?></div>
    <?php endif; ?>

    <?php if (!empty($error)): ?>
        <div class="alert error"><?php echo htmlspecialchars($error); ?></div>
    <?php endif; ?>

    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:20px; gap:12px; flex-wrap:wrap;">
        <h3 style="margin:0;">Student Records</h3>
        <div style="display:flex; gap:8px; align-items:center;">
            <a href="admin_add_student.php" class="btn btn-primary">Add Student</a>
            <a href="admin_logout.php" class="btn btn-outline">Logout</a>
        </div>
    </div>

    <div class="panel" style="margin-bottom:24px;">
        <table style="width:100%; border-collapse:collapse;">
            <thead>
                <tr style="text-align:left; border-bottom:1px solid #e5e7eb;">
                    <th style="padding:10px;">Student ID</th>
                    <th style="padding:10px;">Name</th>
                    <th style="padding:10px;">Email</th>
                    <th style="padding:10px;">Course</th>
                    <th style="padding:10px;">Year</th>
                    <th style="padding:10px;">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($students && $students->num_rows > 0): ?>
                    <?php while ($student = $students->fetch_assoc()): ?>
                        <tr style="border-bottom:1px solid #f1f5f9;">
                            <td style="padding:10px; font-weight:bold;">
                                <?php echo htmlspecialchars($student["student_id"]); ?>
                            </td>
                            <td style="padding:10px;">
                                <?php echo htmlspecialchars($student["first_name"] . " " . $student["last_name"]); ?>
                            </td>
                            <td style="padding:10px;">
                                <?php echo htmlspecialchars($student["email"]); ?>
                            </td>
                            <td style="padding:10px;">
                                <?php echo htmlspecialchars($student["course"]); ?>
                            </td>
                            <td style="padding:10px;">
                                <?php echo htmlspecialchars($student["year_level"]); ?>
                            </td>
                            <td style="padding:10px;">
                                <a href="admin_edit_student.php?id=<?php echo (int)$student['id']; ?>" class="btn btn-outline" style="margin-right:6px;">Edit</a>
                                <form method="POST" action="admin_delete_student.php" style="display:inline;" onsubmit="return confirm('Delete this student?');">
                                    <input type="hidden" name="id" value="<?php echo (int)$student['id']; ?>">
                                    <button type="submit" class="btn btn-danger">Delete</button>
                                </form>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="6" style="padding:14px; text-align:center; color:#6b7280;">
                            No students found.
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <div class="panel">
        <h3>Send Inbox Message</h3>
        <form method="POST">
            <div class="form-grid">
                <div class="form-group">
                    <label>Select Student</label>
                    <select name="student_id" required>
                        <option value="">Choose a student</option>
                        <?php
                        $students_list = $conn->query("SELECT * FROM students ORDER BY last_name, first_name");
                        while ($row = $students_list->fetch_assoc()):
                        ?>
                            <option value="<?php echo htmlspecialchars($row["id"]); ?>">
                                <?php echo htmlspecialchars($row["student_id"] . " - " . $row["first_name"] . " " . $row["last_name"]); ?>
                            </option>
                        <?php endwhile; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label>Title</label>
                    <input type="text" name="title" placeholder="Message title" required>
                </div>
            </div>

            <div class="form-group">
                <label>Message</label>
                <textarea name="message" rows="5" placeholder="Write message here..." required></textarea>
            </div>

            <button type="submit" name="send_message" class="btn btn-primary">Send to Inbox</button>
        </form>
    </div>

    <?php include "includes/footer.php"; ?>
            <form method="POST">
                <div class="form-grid">
                    <div class="form-group">
                        <label>Select Student</label>
                        <select name="student_id" required>
                            <option value="">Choose a student</option>
                            <?php
                            $students_list = $conn->query("SELECT * FROM students ORDER BY last_name, first_name");
                            while ($row = $students_list->fetch_assoc()):
                            ?>
                                <option value="<?php echo htmlspecialchars($row["id"]); ?>">
                                    <?php echo htmlspecialchars($row["student_id"] . " - " . $row["first_name"] . " " . $row["last_name"]); ?>
                                </option>
                            <?php endwhile; ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label>Title</label>
                        <input type="text" name="title" placeholder="Message title" required>
                    </div>
                </div>

                <div class="form-group">
                    <label>Message</label>
                    <textarea name="message" rows="5" placeholder="Write message here..." required></textarea>
                </div>

                <button type="submit" name="send_message" class="btn btn-primary">Send to Inbox</button>
            </form>
        </div>
    </div>
</div>

</body>
</html>
