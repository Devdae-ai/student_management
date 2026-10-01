<?php

require_once "config/db.php";
require_once "includes/auth.php";

$id = $_SESSION["student_db_id"];

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $stmt = $conn->prepare(
        "DELETE FROM students
         WHERE id = ?"
    );

    $stmt->bind_param(
        "i",
        $id
    );

    if ($stmt->execute()) {

        session_unset();
        session_destroy();

        header("Location: index.php");
        exit();

    }

}

header("Location: profile.php");
exit();

?>