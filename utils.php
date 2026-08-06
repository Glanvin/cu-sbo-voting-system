<?php

/**
 * Basic Sanitization of data to Prevent XSS(Cross Site Scripting);
 */
function sanitization($data)
{
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

/**
 * When a $email is passed this function goes to a series of checks
 * 
 * empty: Returns a string of empty when $email has no data
 * 
 * too_long: Returns a string when $email is above 60 in character length
 * 
 * invalid_format: Returns a string when pattern of the email isn't a valid format of an $email
 * 
 * valid: Returns a string when the $email has meet of all conditions 
 */
function validateEmail($email)
{
    $cleanEmail = sanitization($email);

    if (empty($cleanEmail)) {
        return "empty";
    }

    if (strlen($cleanEmail) > 60) {
        return "too_long";
    }

    $pattern = "/^[\w\-\.]+@([\w\-]+\.)+[\w\-]{2,4}$/";
    if (!preg_match($pattern, $cleanEmail)) {
        return "invalid_format";
    }

    return "valid";
}