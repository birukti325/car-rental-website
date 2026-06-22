<?php
// 1. SHOW ERRORS (Prevents White Screen)
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();

// 2. DATABASE CONNECTION
$conn = mysqli_connect("localhost", "root", "", "car_rental_system");
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// 3. AUTO-UPDATE TABLE STRUCTURE
$create_table_query = "CREATE TABLE IF NOT EXISTS `users` (
    `id` INT(11) NOT NULL AUTO_INCREMENT,
    `name` VARCHAR(100) NOT NULL,
    `email` VARCHAR(100) NOT NULL,
    `password` VARCHAR(255) NOT NULL,
    `is_admin` TINYINT(1) DEFAULT 0,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";
mysqli_query($conn, $create_table_query);

$error_message = "";
$success_message = "";

// 4. HANDLE FORM SUBMISSION
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $first_name = mysqli_real_escape_string($conn, $_POST['first_name']);
    $last_name = mysqli_real_escape_string($conn, $_POST['last_name']);
    $full_name = $first_name . " " . $last_name;
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $pass = $_POST['password'];
    $conf_pass = $_POST['confirm_password'];

    if ($pass !== $conf_pass) {
        $error_message = "Passwords do not match!";
    } else {
        $hashed_password = password_hash($pass, PASSWORD_DEFAULT);
        
        $sql = "INSERT INTO users (name, email, password, is_admin) VALUES ('$full_name', '$email', '$hashed_password', 0)";
        
        if (mysqli_query($conn, $sql)) {
            $success_message = "Account created! You can now login.";
            header("refresh:2;url=login.php"); // Redirect after 2 seconds
        } else {
            if (mysqli_errno($conn) == 1062) {
                $error_message = "This email is already registered.";
            } else {
                $error_message = "Error: " . mysqli_error($conn);
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Register - REVS</title>
    <style>
        body { background: #111; color: white; font-family: sans-serif; display: flex; justify-content: center; align-items: center; min-height: 100vh; margin: 0; }
        .card { background: #1a1a1a; padding: 30px; border-radius: 10px; width: 400px; border: 1px solid #333; }
        .nav-logo { font-size: 24px; font-weight: 900; text-align: center; margin-bottom: 20px; text-transform: uppercase; }
        .nav-logo span { color: #d62828; }
        input { width: 100%; padding: 10px; margin: 8px 0; background: #222; border: 1px solid #444; color: white; box-sizing: border-box; border-radius: 5px; }
        button { width: 100%; padding: 12px; background: #d62828; color: white; border: none; font-weight: bold; cursor: pointer; border-radius: 5px; margin-top: 10px; }
        .error { color: #d62828; text-align: center; font-size: 14px; margin-bottom: 10px; }
        .success { color: #00ff88; text-align: center; font-size: 14px; margin-bottom: 10px; }
    </style>
</head>
<body>
    <div class="card">
        <div class="nav-logo">RE<span>VS</span></div>
        <h2 style="text-align:center">Create <span>Account</span></h2>

        <?php if($error_message): ?> <div class="error"><?php echo $error_message; ?></div> <?php endif; ?>
        <?php if($success_message): ?> <div class="success"><?php echo $success_message; ?></div> <?php endif; ?>

        <form method="POST">
            <div style="display:flex; gap:10px;">
                <input type="text" name="first_name" placeholder="First Name" required>
                <input type="text" name="last_name" placeholder="Last Name" required>
            </div>
            <input type="email" name="email" placeholder="Email Address" required>
            <input type="password" name="password" placeholder="Password" required>
            <input type="password" name="confirm_password" placeholder="Confirm Password" required>
            <button type="submit">CREATE ACCOUNT</button>
        </form>
        <p style="text-align:center; font-size: 13px; color: #888;">Already have an account? <a href="login.php" style="color:#d62828;">Login</a></p>
    </div>
</body>
</html>