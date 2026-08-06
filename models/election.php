<?php

// Derive election status from timestamps and the force-close flag.
// Uses strtotime() so comparison is numeric, not a fragile string match.
function computeElectionStatus($open_at, $close_at, $is_closed)
{
    if ($is_closed == 1) {
        return 'closed';
    }

    $now = time();
    $open_ts = strtotime($open_at);
    $close_ts = strtotime($close_at);

    if ($now < $open_ts) {
        return 'upcoming';
    }

    if ($now <= $close_ts) {
        return 'ongoing';
    }

    // Past the close time and not force-closed — treat as closed
    return 'closed';
}

// Return every election row with a computed status key appended
function getAllElections($conn)
{
    $query = "SELECT * FROM elections ORDER BY open_at DESC";
    $result = mysqli_query($conn, $query);

    $elections = [];

    if ($result && mysqli_num_rows($result) > 0) {
        while ($row = mysqli_fetch_assoc($result)) {
            $row['status'] = computeElectionStatus($row['open_at'], $row['close_at'], $row['is_closed']);
            $elections[]   = $row;
        }
    }

    return $elections;
}

// Return only ongoing elections for a student.
// Matches the student's department exactly OR elections marked University Wide
function getOngoingElectionsForStudent($conn, $department)
{

    $query = "SELECT * FROM elections
              WHERE is_closed  = 0
                AND open_at   <= NOW()
                AND close_at  >= NOW()
                AND (department = '$department' OR department = 'University Wide')
              ORDER BY close_at ASC";

    $result = mysqli_query($conn, $query);

    $elections = [];

    if ($result && mysqli_num_rows($result) > 0) {
        while ($row = mysqli_fetch_assoc($result)) {
            $row['status'] = 'ongoing';
            $elections[] = $row;
        }
    }

    return $elections;
}

// Insert a new election record
function createElection($conn, $name, $department, $open_at, $close_at)
{
    $query = "INSERT INTO elections (name, department, open_at, close_at)
              VALUES ('$name', '$department', '$open_at', '$close_at')";

    return mysqli_query($conn, $query);
}

// Update an existing upcoming election — should not be called on ongoing elections
function updateElection($conn, $id, $name, $department, $open_at, $close_at)
{
    $query = "UPDATE elections
              SET name = '$name', department = '$department',
                  open_at = '$open_at', close_at = '$close_at'
              WHERE id = $id";

    return mysqli_query($conn, $query);
}

// Force-close an ongoing election early
function closeElection($conn, $id)
{
    $query = "UPDATE elections SET is_closed = 1 WHERE id = $id";
    return mysqli_query($conn, $query);
}

// Permanently delete an election — only valid for closed elections
function deleteElection($conn, $id)
{
    $query = "DELETE FROM elections WHERE id = $id";
    return mysqli_query($conn, $query);
}