<?php
session_start();
include 'db_connection.php'; 

if (!isset($_SESSION['user_id'])) {
    echo "Πρέπει να συνδεθείτε για να δείτε τη σελίδα αξιολογήσεων.";
    exit();
}

$rating_error = '';
$rating_success = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $comment = $_POST['comment'];
    $Username = $_POST['Username']; 

    if (empty($comment)) {
        $rating_error = "Παρακαλούμε προσθέστε ένα σχόλιο.";
    } else {
        $query = "INSERT INTO comments (username, comment) VALUES (?, ?)";
        if ($stmt = $conn->prepare($query)) {
            $stmt->bind_param("ss", $username, $comment);
            if ($stmt->execute()) {
                $rating_success = "Ευχαριστούμε για το σχόλιό σας!";
            } else {
                $rating_error = "Παρουσιάστηκε σφάλμα. Δοκιμάστε ξανά.";
            }
            $stmt->close();
        } else {
            $rating_error = "Παρουσιάστηκε σφάλμα κατά την προετοιμασία της δήλωσης SQL.";
        }
    }
}

$comments_query = "SELECT Username, comment FROM comments ORDER BY created_at DESC";
$comments_result = $conn->query($comments_query);
?>

<!DOCTYPE html>
<html lang="el">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rate Us</title>
    <link rel="stylesheet" href="rate_us.css">
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
        <h2>Σχόλια Χρηστών</h2>
        <table id="comments-table">
            <thead>
                <tr>
                    <th>Username</th>
                    <th>Σχόλιο</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($comments_result->num_rows > 0): ?>
                    <?php while($row = $comments_result->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($row['Username']); ?></td>
                            <td><?php echo htmlspecialchars($row['comment']); ?></td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="2">Δεν υπάρχουν σχόλια προς το παρόν.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>

        <h3>Αφήστε ένα Σχόλιο</h3>
        <?php if ($rating_error): ?>
            <p style="color: red;"><?php echo $rating_error; ?></p>
        <?php endif; ?>
        <?php if ($rating_success): ?>
            <p style="color: green;"><?php echo $rating_success; ?></p>
        <?php endif; ?>
        <form id="comment-form" action="rate_us.php" method="POST">
            <label for="comment">Σχόλιο:</label>
            <textarea id="comment" name="comment" required></textarea>
            <button type="submit">Αποστολή</button>
        </form>
    </div>

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

<?php
$conn->close();
?>
