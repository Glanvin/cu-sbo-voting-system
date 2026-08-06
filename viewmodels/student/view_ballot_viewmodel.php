<?php
session_start();

// Only students can access the ballot
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'student') {
    header("location: ../../index.php");
    
}

require '../../connection.php';
include '../../models/election.php';
include '../../models/user.php';
include '../../models/candidate.php';
include '../../models/vote.php';
include '../../utils.php';

refreshActivity($conn, $_SESSION['user_id']);

$voter_id = $_SESSION['user_id'];
$studentFirstname = $_SESSION['firstname'];
$studentLastname = $_SESSION['lastname'];
$studentDepartment = $_SESSION['department'];

// Require a valid election_id in the query string
$election_id = isset($_GET['election_id']) ? (int) $_GET['election_id'] : 0;
if ($election_id === 0) {
    header("location: dashboard.php");
    
}

// Load the election record directly
$elResult = mysqli_query($conn, "SELECT * FROM elections WHERE id = $election_id");
$election = $elResult ? mysqli_fetch_assoc($elResult) : null;

if (!$election) {
    header("location: dashboard.php");
    
}

// Make sure the election is still actively ongoing
$status = computeElectionStatus($election['open_at'], $election['close_at'], $election['is_closed']);
if ($status !== 'ongoing') {
    header("location: dashboard.php");
    
}

// Verify the student is eligible (department match or University Wide)
if ($election['department'] !== 'University Wide' && $election['department'] !== $studentDepartment) {
    header("location: dashboard.php");
    
}

// If the student has already voted, send them straight to the receipt
if (hasSubmittedBallot($conn, $election_id, $voter_id)) {
    header("location: receipt.php?election_id=$election_id");
    
}

// Load candidates grouped by position for ballot rendering
$candidatesByPosition = getCandidatesGroupedByPosition($conn, $election_id);

$ballotMessage = "";

// Handle ballot submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_ballot'])) {

    $votes = isset($_POST['vote']) && is_array($_POST['vote']) ? $_POST['vote'] : [];

    // Record one vote per position — default to abstain if position was skipped
    foreach ($candidatesByPosition as $position => $candidates) {
        $selectedVal = isset($votes[$position]) ? $votes[$position] : 'abstain';
        castVote($conn, $election_id, $voter_id, $position, $selectedVal);
    }

    // Mark the full ballot as submitted
    markBallotSubmitted($conn, $election_id, $voter_id);

    // Store receipt data in session so the receipt page knows this is a fresh submission
    $_SESSION['receipt_fresh'] = true;

    header("location: receipt.php?election_id=$election_id");
    
}