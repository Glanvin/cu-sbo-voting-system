<?php
require '../connection.php';
include '../models/user.php';

session_start();

if (!isset($_SESSION['user_id'])) {
    header("location:login.php");
}

$message = "";

if (isset($_POST['change_password'])) {

    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];
    $user_id = $_SESSION['user_id'];

    if ($new_password === $confirm_password) {

        if (updateUserPassword($conn, $user_id, $new_password)) {

            // Password updated and is_new is 0. Redirect to the correct page.
            $role = $_SESSION['role'];
            if ($role == 'admin') {
                header("location:login.php");
            } elseif ($user == 'moderator') {
                header("location:shared/dashboard.php");
            } else {
                header("location:student/dashboard.php");
            }
        } else {
            $message = "Database error: Could not update password.";
        }
    } else {
        $message = "Error: Passwords do not match. Please try again.";
    }
}
