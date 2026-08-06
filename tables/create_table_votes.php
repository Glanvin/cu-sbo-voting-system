<?php
include '../connection.php';

// One row per voter per position per election.
// candidate_id NULL means the voter abstained for that position.
// UNIQUE KEY prevents a voter from voting twice on the same position.
$sql_create_table_votes = "CREATE TABLE votes (
    id INT(10) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    election_id  INT(6) UNSIGNED NOT NULL,
    voter_id INT(6) UNSIGNED NOT NULL,
    position VARCHAR(100) NOT NULL,
    candidate_id INT(6) UNSIGNED NULL DEFAULT NULL,
    voted_at DATETIME DEFAULT NOW(),
    UNIQUE KEY unique_vote_per_position (election_id, voter_id, position),
    FOREIGN KEY (election_id) REFERENCES elections(id) ON DELETE CASCADE,
    FOREIGN KEY (voter_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (candidate_id) REFERENCES candidates(id) ON DELETE SET NULL
)";

if (mysqli_query($conn, $sql_create_table_votes)) {
    echo "Table Votes Created!";
} else {
    echo "Error: " . mysqli_error($conn);
}
