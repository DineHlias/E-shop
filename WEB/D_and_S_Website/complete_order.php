<?php
session_start();
include 'db_connection.php';

if (!isset($_SESSION['user_id'])) {
    echo "Πρέπει να συνδεθείτε για να ολοκληρώσετε την παραγγελία.";
    exit;
}

$user_id = $_SESSION['user_id'];

$sql = "SELECT c.product_id, c.quantity, p.price, p.availability
        FROM cart c
        JOIN products p ON c.product_id = p.id
        WHERE c.user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param('i', $user_id);
$stmt->execute();
$result = $stmt->get_result();

$cart_items = [];
$initial_total = 0; 

while ($row = $result->fetch_assoc()) {
    $cart_items[] = $row;
    $initial_total += $row['price'] * $row['quantity']; 
}

foreach ($cart_items as $item) {
    $product_id = $item['product_id'];
    $cart_quantity = $item['quantity'];
    $availability = $item['availability'];

    if ($cart_quantity > $availability) {
        echo "<script>alert('Μη επαρκής διαθεσιμότητα για το προϊόν ID: $product_id. Διαθέσιμα: $availability'); window.location.href = 'my_cart.php';</script>";
        exit;
    }
}

$discount_percentage = rand(10, 30) / 100;

$final_total = $initial_total - ($initial_total * $discount_percentage);

echo "<script>
    let initialAmount = $initial_total.toFixed(2);
    let discount = ($discount_percentage * 100).toFixed(0);
    let finalAmount = $final_total.toFixed(2);
    alert('Αρχικό Ποσό: ' + initialAmount + '€\\nΈκπτωση: ' + discount + '%\\nΤελικό Ποσό: ' + finalAmount + '€');
</script>";

foreach ($cart_items as $item) {
    $product_id = $item['product_id'];
    $cart_quantity = $item['quantity'];
    $availability = $item['availability'];
    $new_availability = $availability - $cart_quantity;

    $update_sql = "UPDATE products SET availability = ? WHERE id = ?";
    $update_stmt = $conn->prepare($update_sql);
    $update_stmt->bind_param('ii', $new_availability, $product_id);
    $update_stmt->execute();

    $insert_sql = "INSERT INTO purchases (user_id, product_id, quantity) VALUES (?, ?, ?)";
    $insert_stmt = $conn->prepare($insert_sql);
    $insert_stmt->bind_param('iii', $user_id, $product_id, $cart_quantity);
    $insert_stmt->execute();
}

$delete_cart_sql = "DELETE FROM cart WHERE user_id = ?";
$delete_cart_stmt = $conn->prepare($delete_cart_sql);
$delete_cart_stmt->bind_param('i', $user_id);
$delete_cart_stmt->execute();

echo "<script>alert('Η παραγγελία σας ολοκληρώθηκε!'); window.location.href = 'home.php';</script>";


$stmt->close();
$conn->close();
?>
