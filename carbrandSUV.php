<?php
session_start();
// Database connection
$conn = new mysqli("localhost", "root", "", "car_rental_system");
if ($conn->connect_error) { die("Connection Failed"); }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>SUV Collection - REVS</title>
    <style>
        /* CSS EXACTLY FROM YOUR SUV CODE */
        body, html { margin: 0; padding: 0; font-family: 'Segoe UI', Arial, sans-serif; background-color: #111; color: white; }
        .navbar { display: flex; justify-content: space-between; align-items: center; padding: 20px 50px; background: #000; border-bottom: 1px solid #222; }
        .nav-logo { font-size: 28px; font-weight: 900; text-transform: uppercase; letter-spacing: 2px; }
        .nav-logo span { color: #d62828; }
        
        .container { max-width: 1100px; margin: 50px auto; padding: 0 20px; }
        .header-title { text-align: center; font-size: 40px; margin-bottom: 60px; text-transform: uppercase; }
        .header-title span { color: #d62828; }

        /* Brand Block Styles */
        .listing-item { margin-bottom: 40px; border: 1px solid #222; background: #151515; border-radius: 10px; overflow: hidden; }
        
        .brand-header { display: flex; align-items: center; padding: 25px; cursor: pointer; transition: 0.3s; }
        .brand-header:hover { background: #1a1a1a; }
        
        .brand-logo-area { width: 300px; height: 180px; margin-right: 30px; border-radius: 5px; overflow: hidden; background: #000; }
        .brand-logo-area img { width: 100%; height: 100%; object-fit: cover; }
        
        .brand-info h2 { font-size: 32px; margin: 0; text-transform: uppercase; }
        .brand-info h2 span { color: #d62828; }
        .btn-toggle { margin-top: 15px; display: inline-block; border: 2px solid #d62828; color: #d62828; padding: 8px 25px; font-weight: bold; text-transform: uppercase; }

        /* Dropdown Models */
        .models-dropdown { display: none; background: #0a0a0a; border-top: 1px solid #333; padding: 30px; }
        
        .car-row { display: flex; gap: 30px; margin-bottom: 30px; border-bottom: 1px solid #222; padding-bottom: 30px; }
        .car-row:last-child { border-bottom: none; margin-bottom: 0; }
        
        .car-img { width: 280px; height: 170px; object-fit: cover; border-radius: 8px; border: 1px solid #333; }
        
        .car-details { flex: 1; }
        .car-details h3 { margin: 0 0 10px 0; font-size: 24px; color: white; }
        .car-desc { color: #888; font-size: 14px; margin-bottom: 15px; line-height: 1.5; }
        
        .price-box { font-size: 18px; font-weight: bold; margin-bottom: 20px; }
        .price-box span { color: #d62828; margin-right: 15px; }
        
        .action-buttons { display: flex; gap: 15px; }
        .btn { padding: 10px 30px; border-radius: 4px; text-decoration: none; font-weight: bold; text-transform: uppercase; font-size: 13px; text-align: center; }
        .btn-rent { background: #d62828; color: white; }
        .btn-buy { border: 1px solid #d62828; color: #d62828; }
        .btn-buy:hover { background: #d62828; color: white; }
    </style>
</head>
<body>

<nav class="navbar">
    <div class="nav-logo">RE<span>VS</span></div>
</nav>

<div class="container">
    <h1 class="header-title">SUV <span>Collection</span></h1>

    <?php
    // 1. Get unique brands that have cars categorized as 'SUV'
    $brand_query = "SELECT DISTINCT brand FROM cars WHERE type = 'SUV'";
    $brand_result = $conn->query($brand_query);

    if ($brand_result->num_rows > 0) {
        while ($brand = $brand_result->fetch_assoc()) {
            $bName = $brand['brand'];
            $cleanID = preg_replace('/[^a-zA-Z0-9]/', '', $bName); 
    ?>
        <div class="listing-item">
            <div class="brand-header" onclick="toggleBrand('<?php echo $cleanID; ?>')">
                <div class="brand-logo-area">
                    <img src="brand_logos/<?php echo strtolower($cleanID); ?>.webp" onerror="this.src='brand_logos/default.webp'">
                </div>
                <div class="brand-info">
                    <h2><?php echo $bName; ?> <span>SUV</span></h2>
                    <p style="color:#666;">View available high-performance models</p>
                    <div class="btn-toggle">Expand</div>
                </div>
            </div>

            <div id="<?php echo $cleanID; ?>" class="models-dropdown">
                <?php
                // 2. Fetch all cars for this specific brand that are 'SUV' type
                $car_query = "SELECT * FROM cars WHERE brand = '$bName' AND type = 'SUV'";
                $car_result = $conn->query($car_query);
                
                while ($car = $car_result->fetch_assoc()) {
                ?>
                <div class="car-row">
                    <img src="<?php echo $car['image_path']; ?>" class="car-img" onerror="this.src='uploads/default.jpg'">
                    
                    <div class="car-details">
                        <h3><?php echo $car['model']; ?></h3>
                        <p class="car-desc"><?php echo $car['description']; ?></p>
                        
                        <div class="price-box">
                            Rent: <span>$<?php echo number_format($car['daily_rate']); ?></span>
                            Buy: <span>$<?php echo number_format($car['price']); ?></span>
                        </div>
                        
                        <div class="action-buttons">
    <?php if(isset($_SESSION['user_id'])): ?>
        
        <a href="rent.php?car_id=<?php echo $car['id']; ?>" class="btn btn-rent">Rent Now</a>
        <a href="buy.php?car_id=<?php echo $car['id']; ?>" class="btn btn-buy">Purchase</a>

    <?php else: ?>

        <a href="registration.php" class="btn btn-rent" onclick="alert('Please create an account to Rent a car.');">Rent Now</a>
        <a href="registration.php" class="btn btn-buy" onclick="alert('Please create an account to Purchase a car.');">Purchase</a>

    <?php endif; ?>
</div>
                    </div>
                </div>
                <?php } ?>
            </div>
        </div>
    <?php 
        } 
    } else {
        echo "<p style='text-align:center; color:#666;'>No SUV cars found in the database yet.</p>";
    }
    ?>

</div>

<script>
    function toggleBrand(id) {
        var x = document.getElementById(id);
        if (x.style.display === "block") {
            x.style.display = "none";
        } else {
            x.style.display = "block";
        }
    }
</script>

</body>
</html>