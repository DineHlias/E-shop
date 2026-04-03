<?php
session_start();

// Ακύρωση των cookies
if (isset($_COOKIE['first_name'])) {
    setcookie('first_name', '', time() - 3600, '/');
}

if (isset($_COOKIE['last_name'])) {
    setcookie('last_name', '', time() - 3600, '/');
}

if (isset($_COOKIE['email'])) {
    setcookie('email', '', time() - 3600, '/');
}

if (isset($_COOKIE['username'])) {
    setcookie('username', '', time() - 3600, '/');
}

session_unset(); 
session_destroy(); 

header("Location: login.php");
exit;
