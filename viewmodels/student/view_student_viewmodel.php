<?php
session_start();

// Redirect to login if not authenticated or kick out non students
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'student') {
    header("Location: ../../index.php");
}

require '../../connection.php';
include '../../models/election.php';
include '../../models/user.php';

refreshActivity($conn, $_SESSION['user_id']);

$dbConnected = false;
$dbError = "";
$hasElections = false;
$ongoingElections = [];

$studentFirstname = $_SESSION['firstname'];
$studentLastname = $_SESSION['lastname'];
$studentDepartment = $_SESSION['department'];

if ($conn) {
    $dbConnected = true;

    // Only load elections that are ongoing and belong to the student's department
    $ongoingElections = getOngoingElectionsForStudent($conn, $studentDepartment);

    if (!empty($ongoingElections)) {
        $hasElections = true;
    }
} else {
    $dbError = mysqli_connect_error();
}