<?php

// Fetch all candidates for a given election, joined with their election name
function getAllCandidates($conn)
{
    $query = "SELECT c.*, e.name AS election_name
               FROM candidates c
               JOIN elections e ON c.election_id = e.id
               ORDER BY e.name, c.position, c.fullname";
    $result = mysqli_query($conn, $query);

    $candidates = [];
    if ($result && mysqli_num_rows($result) > 0) {
        while ($row = mysqli_fetch_assoc($result)) {
            $candidates[] = $row;
        }
    }
    return $candidates;
}

// Fetch all candidates for a specific election ordered by position then name
function getCandidatesByElection($conn, $election_id)
{
    $query= "SELECT * FROM candidates
               WHERE election_id = $election_id
               ORDER BY position, fullname";
    $result = mysqli_query($conn, $query);

    $candidates = [];
    if ($result && mysqli_num_rows($result) > 0) {
        while ($row = mysqli_fetch_assoc($result)) {
            $candidates[] = $row;
        }
    }
    return $candidates;
}

// Return candidates grouped by position — used to build the ballot form
function getCandidatesGroupedByPosition($conn, $election_id)
{
    $query = "SELECT * FROM candidates
               WHERE election_id = $election_id
               ORDER BY position, fullname";
    $result = mysqli_query($conn, $query);

    $grouped = [];
    if ($result && mysqli_num_rows($result) > 0) {
        while ($row = mysqli_fetch_assoc($result)) {
            $grouped[$row['position']][] = $row;
        }
    }
    return $grouped;
}

// Insert a new candidate record linked to an election
function addCandidate($conn, $election_id, $fullname, $position, $bio, $school_id, $email, $contact_number)
{
    $query = "INSERT INTO candidates (election_id, fullname, position, bio, school_id, email, contact_number)
              VALUES ($election_id, '$fullname', '$position', '$bio', '$school_id', '$email', '$contact_number')";
    return mysqli_query($conn, $query);
}

// Update an existing candidate record by ID
function updateCandidate($conn, $id, $election_id, $fullname, $position, $bio, $school_id, $email, $contact_number)
{
    $query = "UPDATE candidates
              SET election_id = $election_id,
                  fullname = '$fullname',
                  position = '$position',
                  bio = '$bio',
                  school_id = '$school_id',
                  email = '$email',
                  contact_number = '$contact_number'
              WHERE id = $id";
    return mysqli_query($conn, $query);
}

// Permanently remove a candidate — admin only
function deleteCandidate($conn, $id)
{
    $query = "DELETE FROM candidates WHERE id = $id";
    return mysqli_query($conn, $query);
}
