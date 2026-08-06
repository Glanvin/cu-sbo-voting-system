<?php
require_once '../connection.php';

$sql_create_table_users = "CREATE TABLE users (
    id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    school_id VARCHAR(50) NOT NULL,
    firstname VARCHAR(50) NOT NULL,
    lastname VARCHAR(50) NOT NULL,
    email VARCHAR(100) NOT NULL,
    password VARCHAR(255) NOT NULL,
    role ENUM('student', 'moderator', 'admin') DEFAULT 'student',
    is_online INT(1) DEFAULT 0,
    session_id VARCHAR(255) NULL,
    last_activity DATETIME NULL DEFAULT NULL,
    deparment VARCHAR(50) NOT NULL
)";

if(mysqli_query($conn, $sql_create_table_users)) {
    echo "Table Users Created!";
}else {
    echo "Error: " . mysqli_errno($conn);
}