<?php

// Insert a single vote row for one position.
// candidate_id = NULL records an abstain for that position.
function castVote($conn, $election_id, $voter_id, $position, $candidate_val)
{
    $position = mysqli_real_escape_string($conn, $position);

    if ($candidate_val === null || $candidate_val === '' || $candidate_val === 'abstain') {
        $query = "INSERT INTO votes (election_id, voter_id, position, candidate_id)
                  VALUES ('$election_id', '$voter_id', '$position', NULL)";
    } else {
        $candidate_id = (int) $candidate_val;
        $query = "INSERT INTO votes (election_id, voter_id, position, candidate_id)
                  VALUES ('$election_id', '$voter_id', '$position', '$candidate_id')";
    }
    return mysqli_query($conn, $query);
}

// Mark the voter's full ballot as submitted called once after all positions are cast
function markBallotSubmitted($conn, $election_id, $voter_id)
{
    $query = "INSERT INTO voter_submissions (election_id, voter_id)
              VALUES ('$election_id', '$voter_id')";
    return mysqli_query($conn, $query);
}

// Returns true if the voter has already submitted a complete ballot for this election
function hasSubmittedBallot($conn, $election_id, $voter_id)
{
    $query = "SELECT id FROM voter_submissions
               WHERE election_id = '$election_id' AND voter_id = '$voter_id'";
    $result = mysqli_query($conn, $query);
    return $result && mysqli_num_rows($result) > 0;
}

// Retrieve all votes cast by a voter for an election used to build the receipt
function getVotesByVoter($conn, $election_id, $voter_id)
{
    $query = "SELECT v.position, v.candidate_id, v.voted_at,
                      c.fullname AS candidate_name
               FROM votes v
               LEFT JOIN candidates c ON v.candidate_id = c.id
               WHERE v.election_id = $election_id AND v.voter_id = '$voter_id'
               ORDER BY v.position";
    $result = mysqli_query($conn, $query);

    $votes = [];
    if ($result && mysqli_num_rows($result) > 0) {
        while ($row = mysqli_fetch_assoc($result)) {
            $votes[] = $row;
        }
    }
    return $votes;
}

// Return vote counts per candidate, keyed by position used in the reports tally
function getVoteTallyByElection($conn, $election_id)
{
    $query = "SELECT c.position, c.fullname, c.id AS candidate_id,
                      COUNT(v.id) AS votes
               FROM candidates c
               LEFT JOIN votes v ON v.candidate_id = c.id AND v.election_id = '$election_id'
               WHERE c.election_id = '$election_id'
               GROUP BY c.id, c.position, c.fullname
               ORDER BY c.position, votes DESC";
    $result = mysqli_query($conn, $query);

    $tally = [];
    if ($result && mysqli_num_rows($result) > 0) {
        while ($row = mysqli_fetch_assoc($result)) {
            $tally[$row['position']][] = $row;
        }
    }
    return $tally;
}

// Count abstain votes for a single position in an election
function countAbstainsByPosition($conn, $election_id, $position)
{
    $position = mysqli_real_escape_string($conn, $position);
    $query = "SELECT COUNT(*) AS total FROM votes
                 WHERE election_id = '$election_id'
                   AND position    = '$position'
                   AND candidate_id IS NULL";
    $result   = mysqli_query($conn, $query);
    if ($result) {
        $row = mysqli_fetch_assoc($result);
        return (int) $row['total'];
    }
    return 0;
}

// Count how many voters have submitted a completed ballot for an election
function countSubmissions($conn, $election_id)
{
    $query = "SELECT COUNT(*) AS total FROM voter_submissions WHERE election_id = $election_id";
    $result = mysqli_query($conn, $query);
    if ($result) {
        $row = mysqli_fetch_assoc($result);
        return (int) $row['total'];
    }
    return 0;
}

// Count total eligible student voters based on election department scope
function countEligibleVoters($conn, $election_department)
{
    if ($election_department === 'University Wide') {
        $query = "SELECT COUNT(*) AS total FROM users WHERE role = 'student'";
    } else {
        $dept  = mysqli_real_escape_string($conn, $election_department);
        $query = "SELECT COUNT(*) AS total FROM users WHERE role = 'student' AND department = '$dept'";
    }
    $result = mysqli_query($conn, $query);
    if ($result) {
        $row = mysqli_fetch_assoc($result);
        return (int) $row['total'];
    }
    return 0;
}
