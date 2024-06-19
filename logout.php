<?php
session_start();

if (isset($_SESSION['student_id'])) {
    unset($_SESSION['student_id']);
    unset($_SESSION['account_type']);
    session_destroy();
}

header("Location: login.php");
exit;
?>