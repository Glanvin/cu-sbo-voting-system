<?php
session_start();

// Only students can view receipts
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'student') {
    header("Location: ../../index.php");
    exit;
}

require '../../connection.php';
include '../../models/election.php';
include '../../models/user.php';
include '../../models/vote.php';
include '../../utils.php';

refreshActivity($conn, $_SESSION['user_id']);

$voter_id = $_SESSION['user_id'];
$studentFirstname = $_SESSION['firstname'];
$studentLastname = $_SESSION['lastname'];
$studentDept = $_SESSION['department'];

$election_id = isset($_GET['election_id']) ? (int) $_GET['election_id'] : 0;
if ($election_id === 0) {
    header("Location: dashboard.php");
}

// Load the election record
$elResult = mysqli_query($conn, "SELECT * FROM elections WHERE id = $election_id");
$election = $elResult ? mysqli_fetch_assoc($elResult) : null;

if (!$election) {
    header("Location: dashboard.php");
}

// Check that the voter actually submitted for this election
if (!hasSubmittedBallot($conn, $election_id, $voter_id)) {
    header("Location: dashboard.php");
}

// Load the voter's actual choices from the database
$voterVotes = getVotesByVoter($conn, $election_id, $voter_id);

// Determine whether this is a fresh submission or a revisit
$isFresh = isset($_SESSION['receipt_fresh']) && $_SESSION['receipt_fresh'] === true;
unset($_SESSION['receipt_fresh']);

// Get submission time from the DB
$subResult  = mysqli_query($conn, "SELECT submitted_at FROM voter_submissions WHERE election_id = $election_id AND voter_id = $voter_id");
$submission = $subResult ? mysqli_fetch_assoc($subResult) : null;
$submittedAt = $submission ? date('F d, Y \a\t h:i A', strtotime($submission['submitted_at'])) : date('F d, Y \a\t h:i A');

// Fetch the voter's school_id for the receipt
$voterData = mysqli_query($conn, "SELECT school_id FROM users WHERE id = $voter_id");
$voterRow = $voterData ? mysqli_fetch_assoc($voterData) : null;
$schoolId = $voterRow ? $voterRow['school_id'] : '—';

// Count totals for the summary line
$totalPositions = count($voterVotes);
$abstainCount   = 0;
foreach ($voterVotes as $v) {
    if ($v['candidate_id'] === null) $abstainCount++;
}
$votedCount = $totalPositions - $abstainCount;
?>