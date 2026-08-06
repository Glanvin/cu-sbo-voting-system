<?php
include '../connection.php';

// Tracks completed ballot submissions one row per voter per election.
// Querying this table is the definitive way to check if someone has already voted.
$sql_create_table_voter_submissions = "CREATE TABLE voter_submissions (
    id INT(10) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    election_id INT(6) UNSIGNED NOT NULL,
    voter_id INT(6) UNSIGNED NOT NULL,
    submitted_at DATETIME DEFAULT NOW(),
    UNIQUE KEY unique_submission (election_id, voter_id),
    FOREIGN KEY (election_id) REFERENCES elections(id) ON DELETE CASCADE,
    FOREIGN KEY (voter_id) REFERENCES users(id) ON DELETE CASCADE
)";

if (mysqli_query($conn, $sql_create_table_voter_submissions)) {
    echo "Table Voter Submissions Created!";
} else {
    echo "Error: " . mysqli_error($conn);
}