<?php

session_start();

if (!isset($_SESSION['student_db_id'])) {
    header("Location: login.php");
    exit();
}

?>