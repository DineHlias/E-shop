<?php
session_start();
include 'db_connection.php';

if (!isset($_SESSION['user_id'])) {
    echo "Πρέπει να συνδεθείτε για να αφαιρέσετε προϊόντα από το καλάθι.";
    exit();
}

if (isset($_GET['cart_id'])) {
    $cart_id = $_GET['cart_id'];

    $sql = "DELETE FROM cart WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('i', $cart_id);

    if ($stmt->execute()) {
        header("Location: my_cart.php");
        exit();
    } else {
        echo "Σφάλμα κατά την αφαίρεση του προϊόντος.";
    }

    $stmt->close();
}

$conn->close();
?>
