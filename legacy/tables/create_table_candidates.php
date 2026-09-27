<?php
include '../connection.php';

// Candidates belong to a specific election via foreign key.
// ON DELETE CASCADE removes candidates when their parent election is deleted.
$sql_create_table_candidates = "CREATE TABLE candidates (
    id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    election_id INT(6) UNSIGNED NOT NULL,
    fullname VARCHAR(150) NOT NULL,
    position VARCHAR(100) NOT NULL,
    bio TEXT NOT NULL,
    school_id VARCHAR(50) NOT NULL,
    email VARCHAR(100)    NOT NULL,
    contact_number VARCHAR(20) NOT NULL,
    FOREIGN KEY (election_id) REFERENCES elections(id) ON DELETE CASCADE
)";

if (mysqli_query($conn, $sql_create_table_candidates)) {
    echo "Table Candidates Created!";
} else {
    echo "Error: " . mysqli_error($conn);
}