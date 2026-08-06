<?php
require "config.php";

$conn = mysqli_connect(SERVER_NAME, USERNAME, PASSWORD, DB_NAME);
if($conn->connect_error) {
    die("Error:" . $conn->error);
}

// Force PHP and MySQL into the same timezone so date comparisons
// are always consistent regardless of the server environment
// Philippine Standard Time
mysqli_query($conn, "SET time_zone = '+08:00'");   
date_default_timezone_set('Asia/Manila');
?>