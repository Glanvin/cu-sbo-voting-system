<?php
// Fetch a single user by email and password for login
function getUser($conn, $email, $password)
{
    $query  = "SELECT * FROM users WHERE email = '$email' AND password = '$password'";
    $result = mysqli_query($conn, $query);

    if ($result && mysqli_num_rows($result) > 0) {
        return mysqli_fetch_assoc($result);
    }

    return false;
}

// Fetch a single user by their ID used to reload fresh session data
function getUserById($conn, $user_id)
{
    $query  = "SELECT * FROM users WHERE id = $user_id";
    $result = mysqli_query($conn, $query);

    if ($result && mysqli_num_rows($result) > 0) {
        return mysqli_fetch_assoc($result);
    }

    return false;
}

// Mark the user as online, store their session ID, and stamp the activity time
function setSession($conn, $user_id, $current_session)
{
    $query = "UPDATE users 
              SET is_online = 1, session_id = '$current_session', last_activity = NOW() 
              WHERE id = '$user_id'";
    mysqli_query($conn, $query);
}

// Set the user as offline and clear their session when they log out
function setUserOffline($conn, $user_id)
{
    $query = "UPDATE users SET is_online = 0, session_id = NULL, last_activity = NULL WHERE id = '$user_id'";
    mysqli_query($conn, $query);
}

// Call this on every page load to keep the activity timestamp fresh
function refreshActivity($conn, $user_id)
{
    $query = "UPDATE users SET last_activity = NOW() WHERE id = '$user_id'";
    mysqli_query($conn, $query);
}

// Check if a user has an active session on another device.
// A session is only considered truly active if last_activity
// was within the last 30 minutes  handles closed tab or forgotten sessions.
function isSessionActiveElsewhere($conn, $user_id, $current_session)
{
    $query = "SELECT session_id, last_activity FROM users WHERE id = '$user_id'";
    $result = mysqli_query($conn, $query);

    if (!$result || mysqli_num_rows($result) === 0) {
        return false;
    }

    $row = mysqli_fetch_assoc($result);

    // No stored session not active anywhere
    if (empty($row['session_id'])) {
        return false;
    }

    // Same device/browser not a conflict
    if ($row['session_id'] === $current_session) {
        return false;
    }

    // Different session ID found only block if they were active recently
    // Within 30 minutes Anything older means the tab was just closed/forgotten.
    if (!empty($row['last_activity'])) {
        $lastSeen = strtotime($row['last_activity']);
        $thirtyMins  = 30 * 60;

        if ((time() - $lastSeen) > $thirtyMins) {
            // Stale session clear it and allow login
            setUserOffline($conn, $user_id);
            return false;
        }
    }

    return true;
}

// Return all users except the currently logged-in admin
function getUserAll($conn, $current_user_id)
{
    $query  = "SELECT * FROM users WHERE id != $current_user_id";
    $result = mysqli_query($conn, $query);

    $users = [];

    if ($result && mysqli_num_rows($result) > 0) {
        while ($row = mysqli_fetch_assoc($result)) {
            $users[] = $row;
        }
    }

    return $users;
}

// Update the password and flip is_new to 0 after first login
function updateUserPassword($conn, $user_id, $new_password)
{
    $query  = "UPDATE users SET password = '$new_password', is_new = 0 WHERE id = $user_id";
    $result = mysqli_query($conn, $query);

    return $result;
}

// Write an action to the audit_logs table for traceability
function logAction($conn, $admin_id, $action, $details)
{
    $admin_id = (int) $admin_id;
    $query    = "INSERT INTO audit_logs (admin_id, action, details) VALUES ($admin_id, '$action', '$details')";
    mysqli_query($conn, $query);
}

// Insert a new user now includes firstname, lastname, and department
function addUser($conn, $school_id, $firstname, $lastname, $email, $password, $role, $department)
{
    $query = "INSERT INTO users (school_id, firstname, lastname, email, password, role, department, is_new)
              VALUES ('$school_id', '$firstname', '$lastname', '$email', '$password', '$role', '$department', 1)";

    return mysqli_query($conn, $query);
}

// Update an existing user now includes firstname, lastname, and department
function updateUser($conn, $id, $school_id, $firstname, $lastname, $email, $password, $role, $department)
{
    if (!empty($password)) {
        $query = "UPDATE users
                  SET school_id = '$school_id', firstname = '$firstname', lastname = '$lastname',
                      email = '$email', role = '$role', department = '$department', password = '$password'
                  WHERE id = $id";
    } else {
        $query = "UPDATE users
                  SET school_id = '$school_id', firstname = '$firstname', lastname = '$lastname',
                      email = '$email', role = '$role', department = '$department'
                  WHERE id = $id";
    }

    return mysqli_query($conn, $query);
}

// Remove a user record permanently
function deleteUser($conn, $id)
{
    $query = "DELETE FROM users WHERE id = $id";
    return mysqli_query($conn, $query);
}