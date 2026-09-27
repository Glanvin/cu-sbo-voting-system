<?php
session_start();

if (!isset($_SESSION['role']) || !in_array($_SESSION['role'], ['admin', 'moderator'])) {
    header("Location: ../../index.php");
    exit;
}

require '../../connection.php';
include '../../models/election.php';
include '../../models/user.php';
include '../../models/vote.php';
include '../../utils.php';

refreshActivity($conn, $_SESSION['user_id']);

$dbConnected = false;
$dbError = "";
$allElections = [];
$selectedElection = null;
$stats = ['total_voters' => 0, 'votes_cast' => 0, 'abstained' => 0];
$tallyPositions = [];
$hasData = false;

if ($conn) {
    $dbConnected = true;
    $allElections = getAllElections($conn);

    // Determine which election to display — default to the first in the list
    $selected_id = isset($_GET['election_id']) ? (int) $_GET['election_id'] : 0;

    if ($selected_id === 0 && !empty($allElections)) {
        $selected_id = $allElections[0]['id'];
    }

    // Find the selected election object
    foreach ($allElections as $e) {
        if ($e['id'] === $selected_id) {
            $selectedElection = $e;
            break;
        }
    }

    if ($selectedElection) {
        $hasData = true;
        $totalVoters = countEligibleVoters($conn, $selectedElection['department']);
        $votesCast = countSubmissions($conn, $selected_id);
        $abstained = max(0, $totalVoters - $votesCast);

        $stats = [
            'total_voters' => $totalVoters,
            'votes_cast'   => $votesCast,
            'abstained'    => $abstained
        ];

        // Build the tally accordion data from live vote counts
        $tallyData  = getVoteTallyByElection($conn, $selected_id);
        $firstEntry = true;

        foreach ($tallyData as $position => $candidates) {
            $abstainCount = countAbstainsByPosition($conn, $selected_id, $position);
            $candidateArr = [];
            foreach ($candidates as $c) {
                $candidateArr[] = [
                    'name'  => $c['fullname'],
                    'votes' => (int) $c['votes']
                ];
            }
            $tallyPositions[] = [
                'id' => preg_replace('/[^a-zA-Z0-9]/', '_', strtolower($position)),
                'title' => $position,
                'total' => $votesCast,
                'abstain' => $abstainCount,
                'open' => $firstEntry,
                'candidates' => $candidateArr
            ];
            $firstEntry = false;
        }
    }
} else {
    $dbError = mysqli_connect_error();
}