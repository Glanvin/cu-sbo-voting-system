<?php
session_start();

// Include your database connection (adjust path if your connection file is elsewhere)
require '../connection.php'; 
require '../models/user.php';

if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];
    
    setUserOffline($conn, $user_id);

}

session_unset();
session_destroy();

header("Location:login.php?message=logged_out");
?>