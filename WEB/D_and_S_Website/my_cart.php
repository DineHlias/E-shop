<?php
session_start();
include 'db_connection.php';

if (!isset($_SESSION['user_id'])) {
    echo "Πρέπει να συνδεθείτε για να δείτε το καλάθι.";
    exit();
}

$user_id = $_SESSION['user_id'];

$sql = "SELECT c.id AS cart_id, p.title, p.price, c.quantity
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
$discount_percentage = rand(10, 30) / 100;

$final_total = $initial_total - ($initial_total * $discount_percentage);
$stmt->close();
?>


<!DOCTYPE html>
<html lang="el">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Cart</title>
    <link rel="stylesheet" href="my_cart.css">
    <link rel="stylesheet" href="css_navbar.css">
    <link rel="stylesheet" href="footer.css">

</head>
<body>
      <nav>
        <div class="navbar">
            <a class="logo">MyShop</a>
            <a href="javascript:void(0);" class="icon">
                &#9776; 
            </a>
            <div id="nav-links" class="nav-links">
                <a href="home.php">Home</a>
                <a href="my_cart.php">My Cart</a>
                <a href="rate_us.php">Rate Us</a>
                <?php
                    if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true) {
                      echo '<a href="logout.php">Logout</a>';
                    } else {
                       echo '<a href="login.php">Login</a>';
                    };
                ?>
            </div>
        </div>
    </nav>

    <div class="container">
        <h1>Το Καλάθι Μου</h1>

        <table>
            <tr>
            <th>Προϊόν</th>
            <th>Τιμή</th>
            <th>Ποσότητα</th>
            <th>Σύνολο</th>
            <th>Ενέργεια</th>
            </tr>
            
            <?php
                $total_cost = 0;
              foreach ($cart_items as $item) {
                $subtotal = $item['price'] * $item['quantity'];
                $total_cost += $subtotal;
                echo "<tr>";
                echo "<td>" . htmlspecialchars($item['title']) . "</td>";
                echo "<td>" . htmlspecialchars($item['price']) . " €</td>";
                echo "<td>" . htmlspecialchars($item['quantity']) . "</td>";
                echo "<td>" . number_format($subtotal, 2) . " €</td>";
                echo "<td><a href='remove_from_cart.php?cart_id=" . $item['cart_id'] . "'>Αφαίρεση</a></td>";
                echo "</tr>";
              }
            ?>
      </table>

      <?php
        $sql = "SELECT * FROM users WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('i', $user_id);
        $stmt->execute();
        $user_result = $stmt->get_result();
        $user_data = $user_result->fetch_assoc();
        $stmt->close();
       ?>

       <h2>Πληροφορίες Χρήστη</h2>

    <form action="update_user_info.php" method="POST">
        <label for="first_name">Όνομα:</label>
        <input type="text" name="first_name" value="<?php echo htmlspecialchars($user_data['first_name']); ?>" required>
        <br>

        <label for="last_name">Επώνυμο:</label>
        <input type="text" name="last_name" value="<?php echo htmlspecialchars($user_data['last_name']); ?>" required>
        <br>

        <label for="email">Email:</label>
        <input type="email" name="email" value="<?php echo htmlspecialchars($user_data['email']); ?>" required>
        <br>

        <label for="address">Διεύθυνση:</label>
        <input type="text" name="address" value="<?php echo htmlspecialchars($user_data['address']); ?>" required>
        <br>

        <button type="submit">Ενημέρωση Στοιχείων</button>
    </form>

    <h4>Αρχικό Ποσό: <?php echo number_format($initial_total, 2); ?>€</h4>
    <h4>Έκπτωση: <?php echo ($discount_percentage * 100); ?>%</h4>
    <h4>Τελικό Ποσό Πληρωμής: <?php echo number_format($final_total, 2); ?>€</h4>
    
    <form method="post" action="complete_order.php">
        <button type="submit">Ολοκλήρωση Παραγγελίας</button>
    </form>

    <footer>
        <div class="footer-container">
            <div class="contact-info">
                <h3>Επικοινωνήστε μαζί μας</h3>
                <p>Διεύθυνση: Καραολή και Δημητρίου 80, Αθήνα, Ελλάδα</p>
                <p>Τηλέφωνο: <a href="tel:+30210414200">+30210414200</a></p>
                <p>Email: <a href="mailto:hliasdine2003@gmail.com">hliasdine2003@gmail.com</a></p>
            </div>
    
            <div class="map">
                <h3>Βρείτε μας στον χάρτη</h3>
                <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3146.5215895866954!2d23.650404374762214!3d37.94160550253424!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x14a1bbe5bb8515a1%3A0x3e0dce8e58812705!2zzqDOsc69zrXPgM65z4PPhM6uzrzOuc6_IM6gzrXOuc-BzrHOuc-Oz4I!5e0!3m2!1sel!2sgr!4v1726395469465!5m2!1sel!2sgr";
                        width="100%" height="200" style="border:0;" allowfullscreen="" loading="lazy"></iframe>
            </div>
        </div>
    </footer>
    
</body>
</html>
