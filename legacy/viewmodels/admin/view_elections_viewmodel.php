<?php
session_start();

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../../index.php");
}

require '../../connection.php';
include '../../models/election.php';
include '../../models/user.php';
include '../../utils.php';


refreshActivity($conn, $_SESSION['user_id']);

$message = "";
$dbConnected = false;
$dbError = "";
$hasElections = false;
$allElections = [];

$admin_id = $_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    if (isset($_POST['create_election'])) {
        $name = sanitization($_POST['election_name']);
        $department = sanitization($_POST['department']);

        // Convert datetime-local value (YYYY-MM-DDTHH:MM) to MySQL DATETIME format
        $open_at = str_replace('T', ' ', $_POST['open_at'])  . ':00';
        $close_at = str_replace('T', ' ', $_POST['close_at']) . ':00';

        if (createElection($conn, $name, $department, $open_at, $close_at)) {
            $message = "Election '$name' has been successfully created!";
            header("location:view_elections.php?success_msg={$message}");
        } else {
            $message = "Error creating election: " . mysqli_error($conn);
        }
    }

    if (isset($_POST['edit_election'])) {
        $id = (int) $_POST['election_id'];
        $name = sanitization($_POST['election_name']);
        $department = sanitization($_POST['department']);
        $open_at = str_replace('T', ' ', $_POST['open_at'])  . ':00';
        $close_at = str_replace('T', ' ', $_POST['close_at']) . ':00';

        if (updateElection($conn, $id, $name, $department, $open_at, $close_at)) {
            $message = "Election '$name' has been successfully updated!";
            header("location:view_elections.php?success_msg={$message}");
        } else {
            $message = "Error updating election: " . mysqli_error($conn);
        }
    }

    if (isset($_POST['close_election'])) {
        $id = (int) $_POST['election_id'];

        if (closeElection($conn, $id)) {
            $message = "The election has been closed early.";
            header("location:view_elections.php?success_msg={$message}");
        } else {
            $message = "Error closing election: " . mysqli_error($conn);
        }
    }

    if (isset($_POST['delete_election'])) {
        $id = (int) $_POST['election_id'];

        if (deleteElection($conn, $id)) {
            $message = "Election record has been permanently deleted.";
            header("location:view_elections.php?success_msg={$message}");
        } else {
            $message = "Error deleting election: " . mysqli_error($conn);
        }
    }

    header("Location: view_elections.php");
}

if ($conn) {
    $dbConnected = true;
    $allElections = getAllElections($conn);

    if (!empty($allElections)) {
        $hasElections = true;
    }
} else {
    $dbError = mysqli_connect_error();
}