<?php
// setup.php
require "config.php";

// Check if database already exists to prevent re-running setup unnecessarily
$check_conn = mysqli_connect(SERVER_NAME, USERNAME, PASSWORD);
$db_check = mysqli_query($check_conn, "SHOW DATABASES LIKE '" . DB_NAME . "'");
if (mysqli_num_rows($db_check) > 0) {
    // If DB exists, check if there's an admin user. If yes, redirect to login.
    mysqli_select_db($check_conn, DB_NAME);
    $admin_check = mysqli_query($check_conn, "SELECT id FROM users WHERE role = 'admin'");
    if ($admin_check && mysqli_num_rows($admin_check) > 0) {
        header("Location: index.php");
        exit();
    }
}
mysqli_close($check_conn);

$messages = [];
$setup_done = false;
$execution_results = [];
$progress_percentage = 0;

// HANDLE DB & TABLE CREATION
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['run_setup'])) {
    $conn = mysqli_connect(SERVER_NAME, USERNAME, PASSWORD);
    
    // Create Database
    mysqli_query($conn, "CREATE DATABASE IF NOT EXISTS " . DB_NAME);
    mysqli_select_db($conn, DB_NAME);
    mysqli_query($conn, "SET time_zone = '+08:00'");

    // Define table queries in the correct order for Foreign Keys
    $tables = [
        'Users' => "CREATE TABLE IF NOT EXISTS users (
            id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            school_id VARCHAR(50) NOT NULL,
            email VARCHAR(100) NOT NULL,
            password VARCHAR(255) NOT NULL,
            role ENUM('student', 'moderator', 'admin') DEFAULT 'student',
            is_online INT(1) DEFAULT 0,
            session_id VARCHAR(255) NULL,
            last_activity DATETIME NULL DEFAULT NULL,
            department VARCHAR(50) NOT NULL
        )",
        'Elections' => "CREATE TABLE IF NOT EXISTS elections (
            id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            name VARCHAR(150) NOT NULL,
            department VARCHAR(100) NOT NULL,
            open_at DATETIME NOT NULL,
            close_at DATETIME NOT NULL,
            is_closed INT(1) DEFAULT 0,
            firstname VARCHAR(100) NOT NULL,
            lastname VARCHAR(100) NOT NULL
        )",
        'Candidates' => "CREATE TABLE IF NOT EXISTS candidates (
            id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            election_id INT(6) UNSIGNED NOT NULL,
            fullname VARCHAR(150) NOT NULL,
            position VARCHAR(100) NOT NULL,
            bio TEXT NOT NULL,
            school_id VARCHAR(50) NOT NULL,
            email VARCHAR(100) NOT NULL,
            contact_number VARCHAR(20) NOT NULL,
            FOREIGN KEY (election_id) REFERENCES elections(id) ON DELETE CASCADE
        )",
        'Voter Submissions' => "CREATE TABLE IF NOT EXISTS voter_submissions (
            id INT(10) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            election_id INT(6) UNSIGNED NOT NULL,
            voter_id INT(6) UNSIGNED NOT NULL,
            submitted_at DATETIME DEFAULT NOW(),
            UNIQUE KEY unique_submission (election_id, voter_id),
            FOREIGN KEY (election_id) REFERENCES elections(id) ON DELETE CASCADE,
            FOREIGN KEY (voter_id) REFERENCES users(id) ON DELETE CASCADE
        )",
        'Votes' => "CREATE TABLE IF NOT EXISTS votes (
            id INT(10) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            election_id INT(6) UNSIGNED NOT NULL,
            voter_id INT(6) UNSIGNED NOT NULL,
            position VARCHAR(100) NOT NULL,
            candidate_id INT(6) UNSIGNED NULL DEFAULT NULL,
            voted_at DATETIME DEFAULT NOW(),
            UNIQUE KEY unique_vote_per_position (election_id, voter_id, position),
            FOREIGN KEY (election_id) REFERENCES elections(id) ON DELETE CASCADE,
            FOREIGN KEY (voter_id) REFERENCES users(id) ON DELETE CASCADE,
            FOREIGN KEY (candidate_id) REFERENCES candidates(id) ON DELETE SET NULL
        )"
    ];

    $total_tables = count($tables);
    $successful_tables = 0;

    // Execute each query and track progress
    foreach ($tables as $table_name => $sql) {
        if (mysqli_query($conn, $sql)) {
            $execution_results[$table_name] = true;
            $successful_tables++;
        } else {
            $execution_results[$table_name] = false;
        }
    }

    $progress_percentage = ($successful_tables / $total_tables) * 100;

    if ($successful_tables == $total_tables) {
        $messages[] = ['type' => 'success', 'icon' => 'check_circle', 'text' => "All system tables initialized successfully."];
        $setup_done = true;
    } else {
        $messages[] = ['type' => 'danger', 'icon' => 'error', 'text' => "Some tables failed to create."];
    }
    
    mysqli_close($conn);
}

// HANDLE ADMIN ACCOUNT CREATION
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['register_admin'])) {
    $conn = mysqli_connect(SERVER_NAME, USERNAME, PASSWORD, DB_NAME);
    
    $school_id = $_POST['school_id'];
    $email = $_POST['email'];
    $pass = $_POST['password']; // Storing as plain text for simplicity as per previous instructions, though hashing is usually recommended
    $dept = $_POST['department'];
    $role = 'admin'; // Force role to admin

    $sql_insert = "INSERT INTO users (school_id, email, password, role, department) VALUES ('$school_id', '$email', '$pass', '$role', '$dept')";

    if (mysqli_query($conn, $sql_insert)) {
        // Redirect to login page immediately upon success
        echo "<script>
                alert('Admin account created successfully! Redirecting to login page...');
                window.location.href = 'index.php';
              </script>";
        exit();
    } else {
        $messages[] = ['type' => 'danger', 'icon' => 'error', 'text' => "Registration failed: User may already exist. Error: " . mysqli_error($conn)];
        // Keep the setup variables populated so the form stays visible
        $setup_done = true;
        $progress_percentage = 100;
        $execution_results = ['Users' => true, 'Elections' => true, 'Candidates' => true, 'Voter Submissions' => true, 'Votes' => true];
    }
    
    mysqli_close($conn);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>System Setup | SBO Voting</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <style>
        body { background-color: #f0f2f5; font-family: 'Segoe UI', sans-serif; }
        .setup-card { max-width: 600px; margin: 50px auto; border: none; border-radius: 20px; box-shadow: 0 15px 35px rgba(0,0,0,0.1); }
        .material-icons { vertical-align: middle; }
        .form-control { border-radius: 10px; padding: 10px 15px; }
        .table-check { font-size: 1.1rem; }
    </style>
</head>
<body>

<div class="container">
    <div class="card setup-card p-4">
        <div class="text-center mb-4">
            <span class="material-icons text-primary" style="font-size: 56px;">auto_fix_high</span>
            <h2 class="fw-bold mt-2">System Setup</h2>
            <p class="text-muted">Prepare the database tables and create the administrator account</p>
        </div>

        <?php foreach ($messages as $msg): ?>
            <div class="alert alert-<?php echo $msg['type']; ?> d-flex align-items-center mb-3">
                <span class="material-icons me-2"><?php echo $msg['icon']; ?></span>
                <div><?php echo $msg['text']; ?></div>
            </div>
        <?php endforeach; ?>

        <?php if (!$setup_done && empty($execution_results)): ?>
            <!-- STEP 1: INITIALIZE DATABASE -->
            <form method="POST">
                <button type="submit" name="run_setup" class="btn btn-primary w-100 py-3 fw-bold rounded-3">
                    <span class="material-icons me-2">settings_power</span> INITIALIZE DATABASE & TABLES
                </button>
            </form>
            
        <?php elseif (!empty($execution_results)): ?>
            <!-- STEP 2: SHOW PROGRESS AND CHECKBOXES -->
            <h5 class="fw-bold mb-3">Database Setup Progress</h5>
            <div class="progress mb-4" style="height: 25px; border-radius: 10px;">
                <div class="progress-bar progress-bar-striped progress-bar-animated bg-success" role="progressbar" 
                     style="width: <?php echo $progress_percentage; ?>%;" 
                     aria-valuenow="<?php echo $progress_percentage; ?>" aria-valuemin="0" aria-valuemax="100">
                    <?php echo round($progress_percentage); ?>% Complete
                </div>
            </div>

            <div class="mb-4">
                <?php foreach ($execution_results as $table => $success): ?>
                    <div class="form-check table-check mb-2">
                        <input class="form-check-input" type="checkbox" value="" disabled <?php echo $success ? 'checked' : ''; ?>>
                        <label class="form-check-label <?php echo $success ? 'text-success' : 'text-danger'; ?>">
                            <strong><?php echo $table; ?> Table</strong> <?php echo $success ? 'created successfully' : 'failed to create'; ?>
                        </label>
                    </div>
                <?php endforeach; ?>
            </div>
            <hr>
        <?php endif; ?>

        <?php if ($setup_done): ?>
            <!-- STEP 3: CREATE ADMIN ACCOUNT -->
            <form method="POST">
                <h5 class="mb-3 fw-bold text-center text-primary">Create Administrator Account</h5>
                <p class="text-center text-muted small mb-4">You must create an admin account before you can log in.</p>
                
                <div class="mb-3">
                    <input type="text" name="school_id" class="form-control" placeholder="Admin ID (e.g. ADMIN-001)" required>
                </div>
                <div class="mb-3">
                    <input type="email" name="email" class="form-control" placeholder="Admin Email" required>
                </div>
                <div class="mb-3">
                    <input type="text" name="department" class="form-control" placeholder="Department (e.g. IT Department)" required>
                </div>
                <div class="mb-4">
                    <input type="password" name="password" class="form-control" placeholder="Password" required>
                </div>
                
                <button type="submit" name="register_admin" class="btn btn-dark w-100 py-3 fw-bold rounded-3">
                    CREATE ADMIN & GO TO LOGIN
                </button>
            </form>
        <?php endif; ?>

    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>