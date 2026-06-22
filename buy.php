<?php
session_start();
$conn = mysqli_connect("localhost", "root", "", "car_rental_system");

$car_id = isset($_GET['car_id']) ? intval($_GET['car_id']) : 0;
$car_model = "";
$price = 0;

if ($car_id > 0) {
    $res = mysqli_query($conn, "SELECT * FROM cars WHERE id = $car_id");
    if ($car = mysqli_fetch_assoc($res)) {
        $car_model = $car['brand'] . " " . $car['model'];
        $price = $car['price'];
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Buy <?php echo $car_model; ?></title>
    <style>
        body { background: #0a0a0a; color: white; font-family: sans-serif; display: flex; justify-content: center; align-items: center; min-height: 100vh; margin: 0; }
        .form-card { background: #151515; padding: 30px; border-radius: 12px; width: 400px; border: 1px solid #333; }
        h2 span { color: #d62828; }
        input { width: 100%; padding: 12px; margin: 10px 0; background: #222; border: 1px solid #444; color: white; border-radius: 6px; box-sizing: border-box; }
        .btn { background: #d62828; color: white; border: none; padding: 15px; width: 100%; border-radius: 6px; font-weight: bold; cursor: pointer; }
        .price-box { background: #222; padding: 15px; text-align: center; border-radius: 6px; margin-bottom: 20px; border: 1px solid #444; }
    </style>
</head>
<body>
    <div class="form-card">
        <h2>Purchase <span><?php echo $car_model; ?></span></h2>
        <div class="price-box">
            Full Price: <span style="font-size: 24px; font-weight: bold; color: #d62828;">$<?php echo number_format($price); ?></span>
        </div>

        <form method="POST" action="process_order.php">
            <input type="hidden" name="car_id" value="<?php echo $car_id; ?>">
            <input type="hidden" name="order_type" value="purchase">
            
            <label>Full Name</label>
            <input type="text" name="full_name" required>
            
            <label>Phone Number</label>
            <input type="text" name="phone" required>
            
            <label>Home Address (For Delivery)</label>
            <input type="text" name="address" required>
            
            <button type="submit" class="btn">PLACE ORDER</button>
        </form>
    </div>
</body>
</html>