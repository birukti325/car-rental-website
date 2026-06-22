<?php
session_start();
$conn = mysqli_connect("localhost", "root", "", "car_rental_system");

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// 1. Fetch Rental History (Joining with cars table to get model/brand)
$rental_query = "SELECT b.*, c.brand, c.model, c.image_path 
                 FROM bookings b 
                 JOIN cars c ON b.car_id = c.id 
                 WHERE b.user_id = '$user_id' 
                 ORDER BY b.booking_date DESC";
$rentals = mysqli_query($conn, $rental_query);

// 2. Fetch Purchase History
$sales_query = "SELECT s.*, c.brand, c.model, c.image_path 
                FROM sales s 
                JOIN cars c ON s.car_id = c.id 
                WHERE s.user_id = '$user_id' 
                ORDER BY s.sale_date DESC";
$sales = mysqli_query($conn, $sales_query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>My Order History - REVS</title>
    <style>
        body { background: #0a0a0a; color: white; font-family: 'Segoe UI', sans-serif; margin: 0; padding: 0; }
        .navbar { display: flex; justify-content: space-between; align-items: center; padding: 20px 50px; background: #000; border-bottom: 1px solid #222; }
        .nav-logo { font-size: 24px; font-weight: 900; text-transform: uppercase; }
        .nav-logo span { color: #d62828; }
        
        .container { max-width: 1000px; margin: 50px auto; padding: 0 20px; }
        h1 { font-size: 32px; text-transform: uppercase; margin-bottom: 40px; }
        h1 span { color: #d62828; }

        .section-title { font-size: 18px; color: #888; text-transform: uppercase; letter-spacing: 2px; margin-bottom: 20px; border-left: 4px solid #d62828; padding-left: 15px; }
        
        .order-card { background: #151515; border: 1px solid #222; border-radius: 12px; display: flex; align-items: center; padding: 20px; margin-bottom: 20px; transition: 0.3s; }
        .order-card:hover { border-color: #444; background: #1a1a1a; }
        
        .car-thumb { width: 120px; height: 80px; object-fit: cover; border-radius: 6px; margin-right: 25px; border: 1px solid #333; }
        
        .order-info { flex: 1; }
        .order-info h3 { margin: 0; font-size: 20px; }
        .order-date { color: #555; font-size: 13px; margin-top: 5px; }
        
        .order-status { padding: 5px 15px; border-radius: 50px; font-size: 12px; font-weight: bold; text-transform: uppercase; margin-right: 30px; }
        .status-confirmed { background: rgba(40, 167, 69, 0.1); color: #28a745; }
        .status-completed { background: rgba(0, 123, 255, 0.1); color: #007bff; }
        
        .order-price { font-size: 22px; font-weight: 900; color: #d62828; width: 150px; text-align: right; }
        .empty-msg { text-align: center; color: #444; padding: 40px; border: 2px dashed #222; border-radius: 12px; margin-bottom: 40px; }
        .btn-back { color: #888; text-decoration: none; font-size: 14px; }
        .btn-back:hover { color: #fff; }
    </style>
</head>
<body>

<nav class="navbar">
    <div class="nav-logo">RE<span>VS</span></div>
    <a href="home.php" class="btn-back">← Back to Showroom</a>
</nav>

<div class="container">
    <h1>Account <span>History</span></h1>

    <div class="section-title">Rental Bookings</div>
    <?php if (mysqli_num_rows($rentals) > 0): ?>
        <?php while($row = mysqli_fetch_assoc($rentals)): ?>
            <div class="order-card">
                <img src="<?php echo $row['image_path']; ?>" class="car-thumb">
                <div class="order-info">
                    <h3><?php echo $row['brand'] . " " . $row['model']; ?></h3>
                    <div class="order-date">Booked on: <?php echo date('M d, Y', strtotime($row['booking_date'])); ?></div>
                    <div class="order-date">Address: <?php echo $row['delivery_address']; ?></div>
                </div>
                <div class="order-status status-confirmed"><?php echo $row['status']; ?></div>
                <div class="order-price">$<?php echo number_format($row['total_price']); ?></div>
            </div>
        <?php endwhile; ?>
    <?php else: ?>
        <div class="empty-msg">No rental history found.</div>
    <?php endif; ?>

    <div style="margin-top: 60px;" class="section-title">Car Purchases</div>
    <?php if (mysqli_num_rows($sales) > 0): ?>
        <?php while($row = mysqli_fetch_assoc($sales)): ?>
            <div class="order-card">
                <img src="<?php echo $row['image_path']; ?>" class="car-thumb">
                <div class="order-info">
                    <h3><?php echo $row['brand'] . " " . $row['model']; ?></h3>
                    <div class="order-date">Purchased on: <?php echo date('M d, Y', strtotime($row['sale_date'])); ?></div>
                    <div class="order-date">Delivery to: <?php echo $row['address']; ?></div>
                </div>
                <div class="order-status status-completed"><?php echo $row['status']; ?></div>
                <div class="order-price">$<?php echo number_format($row['price']); ?></div>
            </div>
        <?php endwhile; ?>
    <?php else: ?>
        <div class="empty-msg">No purchase history found.</div>
    <?php endif; ?>

</div>

</body>
</html>