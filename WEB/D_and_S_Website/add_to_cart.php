<?php
session_start();
include 'db_connection.php';

if (!isset($_SESSION['user_id'])) {
    echo "Πρέπει να συνδεθείτε για να προσθέσετε προϊόντα στο καλάθι.";
    exit();
}

$user_id = $_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $product_id = $_POST['product_id'];
    $quantity = $_POST['quantity'];

    // Έλεγχος για το αν το προϊόν υπάρχει ήδη στο καλάθι
    $query = "SELECT * FROM cart WHERE product_id = ? AND user_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param('ii', $product_id, $user_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // Αν το προϊόν υπάρχει ήδη στο καλάθι, ενημερώνουμε την ποσότητα
        $row = $result->fetch_assoc();
        $new_quantity = $row['quantity'] + $quantity;
        $update_query = "UPDATE cart SET quantity = ? WHERE product_id = ? AND user_id = ?";
        $update_stmt = $conn->prepare($update_query);
        $update_stmt->bind_param('iii', $new_quantity, $product_id, $user_id);
        $update_stmt->execute();
    } else {
        // Αν το προϊόν δεν υπάρχει, το προσθέτουμε στο καλάθι
        $insert_query = "INSERT INTO cart (user_id, product_id, quantity) VALUES (?, ?, ?)";
        $insert_stmt = $conn->prepare($insert_query);
        $insert_stmt->bind_param('iii', $user_id, $product_id, $quantity);
        $insert_stmt->execute();
    }

    header("Location: home.php");
    exit();
}
?>
