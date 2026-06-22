<?php
session_start();
$conn = mysqli_connect("localhost", "root", "", "car_rental_system");

$car_id = isset($_GET['car_id']) ? intval($_GET['car_id']) : 0;
$car_model = "";
$daily_rate = 0;

// Fetch car details by ID
if ($car_id > 0) {
    $res = mysqli_query($conn, "SELECT * FROM cars WHERE id = $car_id");
    if ($car = mysqli_fetch_assoc($res)) {
        $car_model = $car['brand'] . " " . $car['model'];
        $daily_rate = $car['daily_rate'];
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Rent <?php echo $car_model; ?></title>
    <style>
        body { background: #0a0a0a; color: white; font-family: sans-serif; display: flex; justify-content: center; align-items: center; min-height: 100vh; margin: 0; }
        .form-card { background: #151515; padding: 30px; border-radius: 12px; width: 400px; border: 1px solid #333; }
        h2 span { color: #d62828; }
        input { width: 100%; padding: 12px; margin: 10px 0; background: #222; border: 1px solid #444; color: white; border-radius: 6px; box-sizing: border-box; }
        .btn { background: #d62828; color: white; border: none; padding: 15px; width: 100%; border-radius: 6px; font-weight: bold; cursor: pointer; }
        .price-box { background: #222; padding: 15px; text-align: center; border-radius: 6px; margin-bottom: 20px; border: 1px dashed #d62828; }
    </style>
</head>
<body>
    <div class="form-card">
        <h2>Rent <span><?php echo $car_model; ?></span></h2>
        <div class="price-box">
            Daily Rate: <span style="font-size: 24px; font-weight: bold; color: #d62828;">$<?php echo number_format($daily_rate); ?></span>
        </div>

        <form method="POST" action="process_order.php">
            <input type="hidden" name="car_id" value="<?php echo $car_id; ?>">
            <input type="hidden" name="order_type" value="rental">
            
            <label>Full Name</label>
            <input type="text" name="full_name" required>
            
            <label>Phone Number</label>
            <input type="text" name="phone" placeholder="e.g. +1 234 567 890" required>
            
            <label>Delivery Address</label>
            <input type="text" name="address" placeholder="Full street address" required>
            
            <label>Rental Duration (Days)</label>
            <input type="number" name="duration" min="1" value="1" required>
            
            <button type="submit" class="btn">CONFIRM RENTAL</button>
        </form>
    </div>
</body>
</html>