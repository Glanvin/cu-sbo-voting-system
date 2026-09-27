<?php
include '../connection.php';

$sql_create_table_elections = "CREATE TABLE elections (
    id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(150) NOT NULL,
    department  VARCHAR(100) NOT NULL,
    open_at DATETIME NOT NULL,
    close_at DATETIME NOT NULL,
    is_closed INT(1) DEFAULT 0,
    firstname VARCHAR(100) NOT NULL,
    lastname VARCHAR(100) NOT NULL,
    department VARCHAR(100) NOT NULL

)";

if (mysqli_query($conn, $sql_create_table_elections)) {
    echo "Table Elections Created!";
} else {
    echo "Error: " . mysqli_errno($conn);
}
