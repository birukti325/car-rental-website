<?php
session_start();
$conn = mysqli_connect("localhost", "root", "", "car_rental_system");

if (!$conn) { die("Connection failed: " . mysqli_connect_error()); }

// Initialize variables for the display page
$show_success = false;
$details = [];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $user_id = $_SESSION['user_id'];
    $car_id = intval($_POST['car_id']);
    $order_type = $_POST['order_type']; 
    $phone = mysqli_real_escape_string($conn, $_POST['phone']);
    $address = mysqli_real_escape_string($conn, $_POST['address']);
    $full_name = mysqli_real_escape_string($conn, $_POST['full_name']);

    // Fetch car details for calculation and display
    $car_query = mysqli_query($conn, "SELECT * FROM cars WHERE id = $car_id");
    $car = mysqli_fetch_assoc($car_query);
    $car_name = $car['brand'] . " " . $car['model'];

    if ($order_type == 'rental') {
        $days = intval($_POST['duration']);
        $rate = $car['daily_rate'];
        $total_price = $days * $rate;

        $sql = "INSERT INTO bookings (user_id, car_id, booking_date, total_price, delivery_address, phone, status) 
                VALUES ('$user_id', '$car_id', NOW(), '$total_price', '$address', '$phone', 'Confirmed')";
        
        if (mysqli_query($conn, $sql)) {
            $show_success = true;
            $details = [
                'title' => 'Rental Confirmed',
                'car' => $car_name,
                'summary' => "$days Days @ $" . number_format($rate) . "/day",
                'total' => number_format($total_price),
                'phone' => $phone,
                'address' => $address,
                'name' => $full_name
            ];
        }
    } else {
        $total_price = $car['price'];

        $sql = "INSERT INTO sales (user_id, car_id, sale_date, price, address, phone, status) 
                VALUES ('$user_id', '$car_id', NOW(), '$total_price', '$address', '$phone', 'Completed')";
        
        if (mysqli_query($conn, $sql)) {
            $show_success = true;
            $details = [
                'title' => 'Purchase Complete',
                'car' => $car_name,
                'summary' => "Full Vehicle Ownership",
                'total' => number_format($total_price),
                'phone' => $phone,
                'address' => $address,
                'name' => $full_name
            ];
        }
    }
}

if (!$show_success) {
    header("Location: home.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Order Confirmed - REVS</title>
    <style>
        body { background: #0a0a0a; color: white; font-family: 'Segoe UI', sans-serif; display: flex; justify-content: center; align-items: center; min-height: 100vh; margin: 0; }
        .receipt-card { background: #151515; padding: 40px; border-radius: 20px; border: 1px solid #333; width: 450px; text-align: center; box-shadow: 0 20px 50px rgba(0,0,0,0.5); }
        .success-icon { font-size: 60px; color: #d62828; margin-bottom: 20px; }
        h1 { margin: 0; text-transform: uppercase; letter-spacing: 2px; }
        h1 span { color: #d62828; }
        .car-name { font-size: 22px; color: #888; margin: 10px 0 30px 0; }
        
        .info-grid { text-align: left; background: #1a1a1a; padding: 20px; border-radius: 10px; margin-bottom: 30px; }
        .info-row { display: flex; justify-content: space-between; margin-bottom: 10px; border-bottom: 1px solid #222; padding-bottom: 10px; }
        .info-row:last-child { border: none; }
        .label { color: #666; font-size: 13px; text-transform: uppercase; }
        .value { font-weight: bold; color: #ddd; }
        
        .total-section { border-top: 2px dashed #333; padding-top: 20px; margin-top: 10px; }
        .total-amount { font-size: 32px; font-weight: 900; color: #d62828; }
        
        .btn-home { display: inline-block; background: #d62828; color: white; text-decoration: none; padding: 15px 40px; border-radius: 50px; font-weight: bold; text-transform: uppercase; margin-top: 30px; transition: 0.3s; }
        .btn-home:hover { transform: scale(1.05); box-shadow: 0 0 20px rgba(214, 40, 40, 0.4); }
    </style>
</head>
<body>

<div class="receipt-card">
    <div class="success-icon">✓</div>
    <h1><?php echo $details['title']; ?></h1>
    <div class="car-name"><?php echo $details['car']; ?></div>

    <div class="info-grid">
        <div class="info-row">
            <span class="label">Customer</span>
            <span class="value"><?php echo $details['name']; ?></span>
        </div>
        <div class="info-row">
            <span class="label">Phone</span>
            <span class="value"><?php echo $details['phone']; ?></span>
        </div>
        <div class="info-row">
            <span class="label">Address</span>
            <span class="value"><?php echo $details['address']; ?></span>
        </div>
        <div class="info-row">
            <span class="label">Plan</span>
            <span class="value"><?php echo $details['summary']; ?></span>
        </div>
        
        <div class="total-section">
            <div class="info-row">
                <span class="label" style="color: #fff;">Total Amount</span>
                <span class="total-amount">$<?php echo $details['total']; ?></span>
            </div>
        </div>
    </div>

    <p style="color: #555; font-size: 13px;">A confirmation email has been sent. Our team will contact you shortly for delivery arrangements.</p>

    <a href="home.php" class="btn-home">Back to Showroom</a>
</div>

</body>
</html>