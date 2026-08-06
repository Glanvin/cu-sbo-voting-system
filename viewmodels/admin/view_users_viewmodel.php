<?php
session_start();

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("location: ../../index.php");
}

require '../../connection.php';
include '../../models/user.php';
include '../../utils.php';

refreshActivity($conn, $_SESSION['user_id']);

$dbConnected = false;
$dbError = "";
$hasUsers = false;
$allUsers = [];
$actionMessage = "";

$admin_id = $_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    //code got destroyed needs to be fix
    if (isset($_POST['add_user'])) {
        $school_id = sanitization($_POST['school_id']);
        $firstname = sanitization($_POST['firstname']);
        $lastname = sanitization($_POST['lastname']);
        $email = sanitization($_POST['email']);
        $password = $_POST['password'];
        $role = $_POST['role'];
        $department = $_POST['department'];

        if (addUser($conn, $school_id, $firstname, $lastname, $email, $password, $role, $department)) {
            $actionMessage = "User successfully added.";
        } else {
            $actionMessage = "Error adding user: " . mysqli_error($conn);
        }
    }

    //unable to update table
    if (isset($_POST['edit_user'])) {
        $id = $_POST['user_id'];
        $school_id = sanitization($_POST['school_id']);
        $firstname = sanitization($_POST['firstname']);
        $lastname = sanitization($_POST['lastname']);
        $email = sanitization($_POST['email']);
        $password = $_POST['password'];
        $role = $_POST['role'];
        $department = $_POST['department'];

        if (updateUser($conn, $id, $school_id, $firstname, $lastname, $email, $password, $role, $department)) {
            $actionMessage = "User successfully updated.";
        } else {
            $actionMessage = "Error updating user.";
        }
    }

    if (isset($_POST['delete_user'])) {
        $id             = $_POST['user_id'];
        $email_to_delete = $_POST['delete_email'];

        if (deleteUser($conn, $id)) {
            $actionMessage = "User successfully deleted.";
        } else {
            $actionMessage = "Error deleting user.";
        }
    }

    header("Location: view_users.php");
}

if ($conn) {
    $dbConnected = true;
    $allUsers = getUserAll($conn, $_SESSION['user_id']);

    if (!empty($allUsers)) {
        $hasUsers = true;
    }
} else {
    $dbError = mysqli_connect_error();
}