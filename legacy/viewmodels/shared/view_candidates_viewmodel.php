<?php
session_start();

// Both admin and moderator can manage candidates
if (!isset($_SESSION['role']) || !in_array($_SESSION['role'], ['admin', 'moderator'])) {
    header("Location: ../../index.php");
}

require '../../connection.php';
include '../../models/candidate.php';
include '../../models/election.php';
include '../../models/user.php';
include '../../utils.php';

refreshActivity($conn, $_SESSION['user_id']);

$userRole = $_SESSION['role'];
$actionMessage = "";
$dbConnected = false;
$dbError = "";
$hasCandidates = false;
$allCandidates = [];
$allElections  = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Both admin and moderator can add candidates
    if (isset($_POST['add_candidate'])) {
        $election_id = (int) $_POST['election_id'];
        $fullname = sanitization($_POST['fullname']);
        $position = sanitization($_POST['position']);
        $bio = sanitization($_POST['bio']);
        $school_id = sanitization($_POST['school_id']);
        $email = sanitization($_POST['email']);
        $contact_number = sanitization($_POST['contact_number']);

        if (addCandidate($conn, $election_id, $fullname, $position, $bio, $school_id, $email, $contact_number)) {
            $actionMessage = "Candidate '$fullname' has been successfully added.";
        } else {
            $actionMessage = "Error adding candidate: " . mysqli_error($conn);
        }
    }

    // Both admin and moderator can edit candidates
    if (isset($_POST['edit_candidate'])) {
        $id = (int) $_POST['candidate_id'];
        $election_id = (int) $_POST['election_id'];
        $fullname = sanitization($_POST['fullname']);
        $position = sanitization($_POST['position']);
        $bio = sanitization($_POST['bio']);
        $school_id = sanitization($_POST['school_id']);
        $email = sanitization($_POST['email']);
        $contact_number = sanitization($_POST['contact_number']);

        if (updateCandidate($conn, $id, $election_id, $fullname, $position, $bio, $school_id, $email, $contact_number)) {
            $actionMessage = "Candidate '$fullname' has been successfully updated.";
        } else {
            $actionMessage = "Error updating candidate: " . mysqli_error($conn);
        }
    }

    // Only admin can delete candidates — moderator requests are silently ignored
    if (isset($_POST['delete_candidate'])) {
        if ($userRole === 'admin') {
            $id = (int) $_POST['candidate_id'];
            if (deleteCandidate($conn, $id)) {
                $actionMessage = "Candidate has been permanently deleted.";
            } else {
                $actionMessage = "Error deleting candidate: " . mysqli_error($conn);
            }
        } else {
            $actionMessage = "Access Denied: Moderators cannot delete candidates.";
        }
    }

    header("Location: view_candidates.php");
}

// Fetch data for page display
if ($conn) {
    $dbConnected = true;
    $allCandidates = getAllCandidates($conn);
    $allElections = getAllElections($conn);

    if (!empty($allCandidates)) {
        $hasCandidates = true;
    }
} else {
    $dbError = mysqli_connect_error();
}