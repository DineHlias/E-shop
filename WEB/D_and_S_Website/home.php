<?php
session_start();

$servername = "127.0.0.1:3307"; 
$username = "root"; 
$password = ""; 
$dbname = "d_and_s"; 

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Ανάκτηση προϊόντων από τη βάση δεδομένων
$sql = "SELECT * FROM products";
$result = $conn->query($sql);

// Κώδικας για προσθήκη στο καλάθι
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_to_cart'])) {
    $product_id = $_POST['product_id'];
    $quantity = 1; // Μπορείς να αλλάξεις την ποσότητα εάν το επιθυμείς

    // Εισαγωγή στο καλάθι
    $cart_sql = "INSERT INTO cart (user_id, product_id, quantity) VALUES ('" . $_SESSION['user_id'] . "', '$product_id', '$quantity')";
    if ($conn->query($cart_sql) === TRUE) {
        echo "<script>alert('Το προϊόν προστέθηκε στο καλάθι!');</script>";
    } else {
        echo "<script>alert('Πρόβλημα κατά την προσθήκη στο καλάθι.');</script>";
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="el">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home</title>
    <link rel="stylesheet" href="home.css">
    <link rel="stylesheet" href="css_navbar.css">
    <link rel="stylesheet" href="footer.css">

</head>
<body>
     <nav>
        <div class="navbar">
            <a class="logo">MyShop</a>
            <a href="javascript:void(0);" class="icon" onclick="ToggleMenu()">
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

    <script>
        function toggleMenu() {
            var navbar = document.querySelector('.navbar');
            navbar.classList.toggle('active');
        }
    </script>

    
    <main>
         <h1>My Clothes</h1>
         <div id="clothes-list">
         <?php
        include 'db_connection.php';
        $sql = "SELECT * FROM products";
        $result = mysqli_query($conn, $sql);
        while ($row = mysqli_fetch_assoc($result)) {
      echo "<div class='products'>";
      echo "<img src='".$row['image_url']."' alt='Cloth Image'>";
      echo "<h2>".$row['title']."</h2>";
      echo "<p>Description: ".$row['description']."</p>";
      echo "<p>Price: $".$row['price']."</p>";
      echo "<p>Availability: ".$row['availability']."</p>";
      echo "<form action='add_to_cart.php' method='POST'>";
      echo "<input type='hidden' name='product_id' value='".$row['id']."'>";
      echo "<label for='quantity'>Ποσότητα:</label>";
      echo "<input type='number' name='quantity' id='quantity' value='1' min='1' required>";
      echo "<button type='submit' class='btn'>Προσθήκη στο καλάθι</button>";
      echo "</form>";
      
      echo "</div>";
    }
    ?>
    </div>
   </main>

    <script>
        function addToCart(productId) {
            const formData = new FormData();
            formData.append('product_id', productId);
        
            fetch('add_to_cart.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success') {
                    alert(data.message); 
                } else {
                    alert(data.message); 
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Σφάλμα κατά την προσθήκη στο καλάθι.');
            });
        }
        </script>

    </script>

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
