<?php
session_start();

if (isset($_SESSION['user_id']) && isset($_SESSION['role'])) {
    
    $role = $_SESSION['role'];
    
    if ($role === 'admin') {
        header("Location: views/shared/dashboard.php");
        
    } elseif ($role === 'moderator') {
        header("Location: views/shared/dashbboard.php");
        
    } else {
        header("Location: views/student/dashboard.php");
    }

} else {
    header("Location: views/login.php");
}
?>
