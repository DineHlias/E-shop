<?php
$servername = "localhost:3307"; 
$username = "root";  
$password = ""; 
$dbname = "d_and_s";  

// Δημιουργία σύνδεσης
$conn = new mysqli($servername, $username, $password, $dbname);

// Έλεγχος σύνδεσης
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
$conn->set_charset("utf8");
?>
