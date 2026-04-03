<?php
session_start();
include 'db_connection.php';

$login_error = '';
$register_error = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['Login'])) {
        $Username = $_POST['Username'];
        $Password = $_POST['Password'];

        $query = "SELECT * FROM users WHERE Username = ? AND Password = ?";
        if ($stmt = $conn->prepare($query)) {
            $stmt->bind_param("ss", $Username, $Password);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows > 0) {
                $userData = $result->fetch_assoc();
                setcookie("first_name", $userData['first_name'], time() + (86400 * 30), "/"); // Cookie διάρκειας 30 ημερών
                setcookie("last_name", $userData['last_name'], time() + (86400 * 30), "/");
                setcookie("email", $userData['email'], time() + (86400 * 30), "/");
                setcookie("Username", $userData['Username'], time() + (86400 * 30), "/");
                setcookie("address", $userData['address'], time() + (86400 * 30), "/");

                $_SESSION['loggedin'] = true;
                $_SESSION['user_id'] = $userData['id'];
                $_SESSION['username'] = $userData['Username'];

                header("Location: home.php");
                exit;
            } else {
                $login_error = "Invalid username or password.";
            }
        }
        $stmt->close();
    }

    if (isset($_POST['Register'])) {
        $first_name = $_POST['first_name'];
        $last_name = $_POST['last_name'];
        $username = $_POST['username'];
        $password = $_POST['password'];
        $email = $_POST['email'];
        $address = $_POST['address'];

        if (empty($first_name) || empty($last_name) || empty($username) || empty($password) || empty($email)|| empty($address)) {
            $register_error = "All fields are required.";
        }elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $register_error = "Invalid email format.";
        } else {
            $sql = "INSERT INTO users (first_name, last_name, username, password, email, address) VALUES (?, ?, ?, ?, ?,?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ssssss", $first_name, $last_name, $username, $password, $email, $address);

            if ($stmt->execute()) {
                $register_success = "Registration successful. You can now log in.";
            } else {
                $register_error = "Error: " . $stmt->error;
            }
            $stmt->close();
        }
    }
    $conn->close();

}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="login.css">
    <link rel="stylesheet" href="css_navbar.css">
    <link rel="stylesheet" href="footer.css">
    <title>Login/Register</title>
    <style>
        #register-form { display: none; }
    </style>
</head>
<body>
<button class="hamburger-menu" id="hamburger-menu">
    <i class="fas fa-bars"></i>
</button>
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

<script>
    document.getElementById('hamburger-menu').addEventListener('click', function() {
    var navbar = document.getElementById('navbar');
    navbar.classList.toggle('active');
    });
</script>

    <main>
        <h1>Login/Register</h1>
        <div id="auth-forms">
            <div id="login-form">
                <h2>Login</h2>
                <form action="login.php" method="post">
                    <label for="Username1">Username:</label>
                    <input type="text" id="Username1" name="Username" required>
                    <label for="Password1">Password:</label>
                    <input type="password" id="Password1" name="Password" required>
                    <button type="submit" name="Login">Login</button>
                    <p>If you don't have an account, press <a href="#" id="show-register">here</a></p>
                </form>
                <?php
                if (!empty($login_error)) {
                    echo '<p style="color:red;">' . htmlspecialchars($login_error) . '</p>';
                }
                ?>
            </div>
            <div id="register-form">
                <h2>Register</h2>
                <form action="Login.php" method="post">
                    <label for="first_name">First Name:</label>
                    <input type="text" id="first_name" name="first_name" required>
                    <label for="last_name">Last Name:</label>
                    <input type="text" id="last_name" name="last_name" required>
                    <label for="username">Username:</label>
                    <input type="text" id="username" name="username" required>
                    <label for="password">Password:</label>
                    <input type="password" id="password" name="password" required>
                    <label for="email">Email:</label>
                    <input type="email" id="email" name="email" required>
                    <label for="address">Address:</label>
                    <input type="text" id="address" name="address" required>
                    <button type="submit" name="Register">Register</button>
                    <p>If you have an account, press <a href="#" id="show-login">here</a></p>
                </form>
                <?php
                if (!empty($register_error)) {
                    echo '<p style="color:red;">' . htmlspecialchars($register_error) . '</p>';
                } elseif (!empty($register_success)) {
                    echo '<p style="color:green;">' . htmlspecialchars($register_success) . '</p>';
                }
                ?>
            </div>
        </div>
    </main>

    <script>
        document.getElementById('show-register').addEventListener('click', function(event) {
            event.preventDefault();
            document.getElementById('login-form').style.display = 'none';
            document.getElementById('register-form').style.display = 'block';
        });

        document.getElementById('show-login').addEventListener('click', function(event) {
            event.preventDefault();
            document.getElementById('register-form').style.display = 'none';
            document.getElementById('login-form').style.display = 'block';
        });
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
