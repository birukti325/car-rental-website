<?php
// --- SECURITY UPDATE: SESSION PARAMETERS ---
session_set_cookie_params(0); 
error_reporting(E_ALL);
ini_set('display_errors', 1);

ob_start(); 
session_start();

$conn = mysqli_connect("localhost", "root", "", "car_rental_system");
if (!$conn) { die("Connection failed: " . mysqli_connect_error()); }

$error_message = "";

if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = mysqli_real_escape_string($conn, trim($_POST['email']));
    $password = $_POST['password'];

    $sql = "SELECT * FROM users WHERE email = '$email'";
    $result = mysqli_query($conn, $sql);

    if ($result && mysqli_num_rows($result) > 0) {
        $user = mysqli_fetch_assoc($result);
        
        if (password_verify($password, $user['password'])) {
            
            // --- SECURITY UPDATE: REGENERATE SESSION ID ---
            session_regenerate_id(true);

            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_name'] = $user['name'];
            $_SESSION['is_admin'] = $user['is_admin']; 
            $_SESSION['last_action'] = time(); // Start timer

            header("Location: home.php");
            exit();
        } else {
            $error_message = "Invalid password.";
        }
    } else {
        $error_message = "No account found with that email.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login - REVS</title>
    <style>
        body { background: #111; color: white; font-family: sans-serif; display: flex; justify-content: center; align-items: center; height: 100vh; margin: 0; }
        .login-card { background: #1a1a1a; padding: 40px; border-radius: 10px; width: 350px; border: 1px solid #333; }
        .nav-logo { font-size: 24px; font-weight: 900; text-align: center; margin-bottom: 20px; }
        .nav-logo span { color: #d62828; }
        input { width: 100%; padding: 12px; margin: 10px 0; background: #222; border: 1px solid #444; color: white; box-sizing: border-box; }
        button { width: 100%; padding: 12px; background: #d62828; color: white; border: none; font-weight: bold; cursor: pointer; border-radius: 5px; }
        .error { color: #d62828; font-size: 13px; text-align: center; margin-bottom: 10px; }
    </style>
</head>
<body>
    <div class="login-card">
        <div class="nav-logo">RE<span>VS</span></div>
        <h2 style="text-align:center">Sign <span>In</span></h2>
        
        <?php if ($error_message): ?>
            <div class="error"><?php echo $error_message; ?></div>
        <?php endif; ?>

        <?php if (isset($_GET['reason']) && $_GET['reason'] == 'timeout'): ?>
            <div class="error" style="color: #ffc107;">Session expired for security. Please log in again.</div>
        <?php endif; ?>

        <form method="POST">
            <input type="email" name="email" placeholder="Email Address" required>
            <input type="password" name="password" placeholder="Password" required>
            <button type="submit">LOGIN</button>
        </form>
        <p style="font-size: 12px; text-align: center; color: #888;">New here? <a href="registration.php" style="color: #d62828;">Create account</a></p>
    </div>
</body>
</html>