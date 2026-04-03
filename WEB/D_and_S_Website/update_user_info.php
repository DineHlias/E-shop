<?php
session_start();
include 'db_connection.php';

if (!isset($_SESSION['user_id'])) {
    echo "Πρέπει να συνδεθείτε για να ενημερώσετε τις πληροφορίες σας.";
    exit();
}

$user_id = $_SESSION['user_id'];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $first_name = $_POST['first_name'];
    $last_name = $_POST['last_name'];
    $email = $_POST['email'];
    $address = $_POST['address'];

    $sql = "UPDATE users SET first_name = ?, last_name = ?, email = ?, address = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('ssssi', $first_name, $last_name, $email, $address, $user_id);

    if ($stmt->execute()) {
        echo "<script>alert('Οι πληροφορίες ενημερώθηκαν με επιτυχία.!'); window.location.href = 'my_cart.php';</script>";
    } else {
        echo "Σφάλμα κατά την ενημέρωση των πληροφοριών.";
    }

    $stmt->close();
}

$conn->close();
?>
