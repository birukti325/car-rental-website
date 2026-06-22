<?php
// --- SECURITY UPDATE: SESSION PARAMETERS ---
session_set_cookie_params(0); 
session_start();

// --- SECURITY UPDATE: PREVENT BROWSER CACHING ---
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

// --- SECURITY UPDATE: SESSION TIMEOUT (10 Minutes) ---
$timeout_duration = 600;
if (isset($_SESSION['last_action'])) {
    $elapsed_time = time() - $_SESSION['last_action'];
    if ($elapsed_time >= $timeout_duration) {
        session_unset();
        session_destroy();
        header("Location: login.php?reason=timeout");
        exit();
    }
}
$_SESSION['last_action'] = time();

// Database connection
$conn = mysqli_connect("localhost", "root", "", "car_rental_system");
if (!$conn) { die("Connection failed: " . mysqli_connect_error()); }

// Check login status and roles
$is_logged_in = isset($_SESSION['user_id']);
$user_name = isset($_SESSION['user_name']) ? $_SESSION['user_name'] : "";
$is_admin = (isset($_SESSION['is_admin']) && $_SESSION['is_admin'] == 1);

// Optional: Fetch the 3 newest cars to show on the home page
$cars_query = "SELECT * FROM cars ORDER BY id DESC LIMIT 3";
$cars_result = mysqli_query($conn, $cars_query);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>REVS - Premium Car Rental & Dealership</title>
    <style>
        /* General Styles */
        body, html {
            margin: 0;
            padding: 0;
            font-family: 'Segoe UI', Arial, sans-serif;
            background-color: #111; 
            color: white;
            overflow-x: hidden;
        }

        /* Modern Navbar */
        .navbar {
            position: absolute;
            top: 0;
            width: 100%;
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 20px 50px;
            box-sizing: border-box;
            z-index: 10;
            background: rgba(0,0,0,0.3);
        }

        .nav-logo {
            font-size: 28px;
            font-weight: 900;
            color: white;
            text-transform: uppercase;
            letter-spacing: 2px;
        }

        .nav-logo span { color: #d62828; }

        .nav-links a {
            color: white;
            text-decoration: none;
            margin-left: 30px;
            font-weight: 500;
            transition: 0.3s;
        }

        .nav-links a:hover { color: #d62828; }

        /* Admin Highlight Link */
        .admin-link {
            color: #d62828 !important;
            border: 1px solid #d62828;
            padding: 5px 15px;
            border-radius: 20px;
            font-weight: bold;
        }

        /* Hero Section */
        .hero {
            height: 100vh;
            background: linear-gradient(rgba(0,0,0,0.6), rgba(0,0,0,0.6)), 
                        url('home.webp'); 
            background-size: cover;
            background-position: center;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            text-align: center;
        }

        .hero h1 {
            font-size: 80px;
            margin: 0;
            font-weight: 300;
            text-transform: uppercase;
        }

        .hero h1 span {
            font-weight: 800;
            color: #d62828;
        }

        .hero p {
            font-size: 18px;
            letter-spacing: 2px;
            margin-bottom: 30px;
            max-width: 700px;
            color: #ccc;
        }

        .welcome-msg {
            font-size: 16px;
            color: #d62828;
            margin-bottom: 10px;
            font-weight: 600;
            text-transform: uppercase;
        }

        .btn-container { display: flex; gap: 20px; }

        .btn {
            padding: 15px 40px;
            font-size: 14px;
            font-weight: bold;
            text-transform: uppercase;
            text-decoration: none;
            border-radius: 50px;
            transition: 0.4s ease;
        }

        .btn-primary { background-color: #d62828; color: white; border: 2px solid #d62828; }
        .btn-primary:hover { background-color: transparent; color: #d62828; }

        .btn-secondary { background-color: transparent; color: white; border: 2px solid white; }
        .btn-secondary:hover { background-color: white; color: black; }
    </style>
</head>
<body>

    <nav class="navbar">
        <div class="nav-logo">RE<span>VS</span></div>
        <div class="nav-links">
            <?php if ($is_logged_in): ?>
                <?php if ($is_admin): ?>
                    <a href="admin_add_car.php" class="admin-link">Admin Panel</a>
                <?php endif; ?>
                
                
                <a href="my_orders.php">My History</a>
                <a href="about.html">About</a>
                <a href="logout.php">Logout</a>
            <?php else: ?>
                <a href="login.php">Login</a>
                <a href="registration.php">Register</a>
            <?php endif; ?>
        </div>
    </nav>

    <header class="hero">
        <?php if ($is_logged_in): ?>
            <p class="welcome-msg">Welcome, <?php echo htmlspecialchars($user_name); ?>!</p>
        <?php endif; ?>
        <p>Welcome to</p>
        <h1>RE<span>VS</span></h1>
        <p>THE SMARTEST AND MOST FLEXIBLE CAR RENTAL & DEALERSHIP YOU'VE EVER SEEN</p>
        
        <div class="btn-container">
            <a href="cartype.php" class="btn btn-primary">Browse Cars</a>
            <a href="about.html" class="btn btn-secondary">Our Story</a>
        </div>
    </header>

</body>
</html>