<?php
session_start();
$is_logged_in = isset($_SESSION['user_id']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Car Types - REVS</title>
    <style>
        body, html {
            margin: 0;
            padding: 0;
            font-family: 'Segoe UI', Arial, sans-serif;
            background-color: #111; 
            color: white;
            overflow-x: hidden;
        }

        .navbar {
            display: flex; justify-content: space-between; align-items: center;
            padding: 20px 50px; background: #000; border-bottom: 1px solid #222;
        }
        .nav-logo { font-size: 28px; font-weight: 900; text-transform: uppercase; letter-spacing: 2px; }
        .nav-logo span { color: #d62828; }
        .nav-links a { color: white; text-decoration: none; margin-left: 30px; font-weight: 500; transition: 0.3s; }
        .nav-links a:hover { color: #d62828; }

        .page-header { text-align: center; padding: 60px 20px; }
        .page-header h1 { font-size: 50px; text-transform: uppercase; margin: 0; }
        .page-header span { color: #d62828; }

        /* LISTING LAYOUT STYLES */
        .listing-container {
            max-width: 1100px;
            margin: 0 auto 80px auto;
            padding: 0 20px;
        }

        .listing-item {
            margin-bottom: 80px;
        }

        /* 1. Name above image */
        .listing-name {
            font-size: 32px;
            text-transform: uppercase;
            font-weight: 800;
            margin-bottom: 20px;
            border-left: 4px solid #d62828;
            padding-left: 15px;
            letter-spacing: 1px;
        }

        /* 2. Flex Layout: Image Left, Card Right */
        .listing-content {
            display: flex;
            align-items: center; /* Center vertically */
            gap: 40px;
        }

        .listing-image {
            flex: 1.5; /* Takes up more space */
        }

        .listing-image img {
            width: 100%;
            height: auto;
            border-radius: 5px;
            display: block;
            box-shadow: 0 5px 15px rgba(0,0,0,0.5);
        }

        .listing-card-wrapper {
            flex: 1; /* Takes up less space */
        }

        /* Minimal Card Style */
        .minimal-card {
            background-color: #1a1a1a;
            padding: 30px;
            border: 1px solid #333;
            border-radius: 8px;
            position: relative;
        }

        .minimal-card p {
            color: #ccc;
            line-height: 1.6;
            margin-top: 0;
            margin-bottom: 25px;
        }

        .btn-browse {
            display: inline-block;
            padding: 12px 30px;
            background-color: transparent;
            color: #d62828;
            border: 2px solid #d62828;
            text-decoration: none;
            text-transform: uppercase;
            font-weight: bold;
            font-size: 14px;
            transition: 0.3s;
            cursor: pointer;
        }

        .btn-browse:hover {
            background-color: #d62828;
            color: white;
        }

    </style>
</head>
<body>

    <nav class="navbar">
        <div class="nav-logo">RE<span>VS</span></div>
        <div class="nav-links">
            <a href="home.php">Home</a>
            <a href="about.html">About</a>
            <?php if ($is_logged_in): ?>
                <a href="logout.php">Logout</a>
            <?php else: ?>
                <a href="login.php">Login</a>
            <?php endif; ?>
        </div>
    </nav>

    <div class="page-header">
        <h1>Select Your <span>Type</span></h1>
    </div>

    <div class="listing-container">

        <div class="listing-item">
            <div class="listing-name">SUV</div>
            <div class="listing-content">
                <div class="listing-image">
                    <img src='SUV.webp'alt="SUV">
                </div>
                <div class="listing-card-wrapper">
                    <div class="minimal-card">
                        <p>Perfect for families and off-road adventures. Our SUVs combine luxury interiors with rugged capability, ensuring comfort on every terrain.</p>
                        <a href="carbrandSUV.php" class="btn-browse">Browse SUVs</a>
                    </div>
                </div>
            </div>
        </div>

        <div class="listing-item">
    <div class="listing-name">Sedan</div>
    <div class="listing-content">
        <div class="listing-image">
            <img src='Sedan.webp' alt="Sedan">
        </div>
        <div class="listing-card-wrapper">
            <div class="minimal-card">
                <p>Executive elegance meets efficiency. Ideal for city driving and business trips, offering a smooth ride with premium features.</p>
                <a href="carbrand.php" class="btn-browse">Browse Sedans</a>
            </div>
        </div>
    </div>
</div>

        <div class="listing-item">
            <div class="listing-name">Sport</div>
            <div class="listing-content">
                <div class="listing-image">
                    <img src='Sport.jpg' alt="Sport">
                </div>
                <div class="listing-card-wrapper">
                    <div class="minimal-card">
                        <p>Dominate the asphalt with our curated collection of high-performance machinery. From the precision of German engineering to the raw soul of European supercars, REVS brings you the ultimate driving experience.</p>
                        <a href="carbrandSport.php" class="btn-browse">Browse Sport</a>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="listing-item">
            <div class="listing-name">Truck</div>
            <div class="listing-content">
                <div class="listing-image">
                    <img src='Pickup.webp' alt="Truck">
                </div>
                <div class="listing-card-wrapper">
                    <div class="minimal-card">
                        <p>Heavy-duty power for the toughest jobs. With massive towing capacity and cargo space, these trucks don't compromise.</p>
                        <a href="carbrandTruck.php" class="btn-browse">Browse Trucks</a>
                    </div>
                </div>
            </div>
        </div>

        <div class="listing-item">
            <div class="listing-name">Vans</div>
            <div class="listing-content">
                <div class="listing-image">
                    <img src='Van.webp'alt="Van">
                </div>
                <div class="listing-card-wrapper">
                    <div class="minimal-card">
                        <p>Maximum space for maximum utility. Whether it's moving cargo or transporting a large group, our vans deliver.</p>
                        <a href="carbrandVan.php" class="btn-browse">Browse Vans</a>
                    </div>
                </div>
            </div>
        </div>

    </div>

</body>
</html>