<?php
session_start();

require '../connection.php';
include '../utils.php';
include '../models/user.php';

$email = $password = $message = "";


// New only blocks if actually active in the last 30 minutes
if (isSessionActiveElsewhere($conn, $user['id'], session_id())) {
    $message = "This account is currently active on another device. Please wait 30 minutes or sign out from that device first.";
}

if (isset($_POST['login'])) {
    $email    = sanitization($_POST['email']);
    $password = sanitization($_POST['password']);

    $status = validateEmail($email);

    switch ($status) {
        case 'empty':
            $message = "Email Cannot be Empty";
            break;

        case 'too_long':
            $message = "Email too long";
            break;

        case 'invalid_format':
            $message = "Invalid Format. Please enter a valid Email";
            break;

        case 'valid':
            $user = getUser($conn, $email, $password);

            if ($user) {
                // Block login if the account is already active on another device
                if ($user['is_online'] == 1 && $user['session_id'] != session_id()) {
                    $message = "Access Denied: Your account is currently logged in on another device.";
                    break;
                }

                $user_id = $user['id'];
                $current_session = session_id();

                setSession($conn, $user_id, $current_session);

                // Store identity and role data in session for use across all views
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['role'] = $user['role'];
                $_SESSION['firstname'] = $user['firstname'];
                $_SESSION['lastname'] = $user['lastname'];
                $_SESSION['department'] = $user['department'];
                $_SESSION['last_activity'] = time();

                // First time login force password change before proceeding
                if ($user['is_new'] == 1) {
                    header("location:change_password.php");
                }

                // Route the user to their role appropriate dashboard page
                if ($user['role'] == 'admin') {
                    header("location:../views/shared/dashboard.php");
                } elseif ($user['role'] == 'moderator') {
                    header("location:../views/shared/dashboard.php");
                } else {
                    header("location:../views/student/dashboard.php");
                }
            } else {
                $message = "Invalid university email or password.";
            }
            break;
    }
}
