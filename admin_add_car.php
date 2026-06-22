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

error_reporting(E_ALL);
ini_set('display_errors', 1);

// 1. Security Check
if (!isset($_SESSION['is_admin']) || $_SESSION['is_admin'] != 1) {
    die("Unauthorized access.");
}

// 2. Database Connection
$conn = mysqli_connect("localhost", "root", "", "car_rental_system");
if (!$conn) { die("Connection failed: " . mysqli_connect_error()); }

// --- CAR LOGIC ---
$edit_mode = false;
$update_id = 0;
$brand = $model = $type = $desc = "";
$daily_rate = $price = 0;

// Handle DELETE
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    mysqli_query($conn, "DELETE FROM cars WHERE id=$id");
    header("Location: admin_add_car.php"); 
    exit();
}

// Handle EDIT (Load data)
if (isset($_GET['edit'])) {
    $edit_mode = true;
    $update_id = intval($_GET['edit']);
    $res = mysqli_query($conn, "SELECT * FROM cars WHERE id=$update_id");
    if ($row = mysqli_fetch_assoc($res)) {
        $brand = $row['brand'];
        $model = $row['model'];
        $type = $row['type'];
        $daily_rate = $row['daily_rate'];
        $price = $row['price'];
        $desc = $row['description'];
    }
}

// Handle SAVE (Add or Update)
if (isset($_POST['save_car'])) {
    $brand = mysqli_real_escape_string($conn, $_POST['brand']);
    $model = mysqli_real_escape_string($conn, $_POST['model']);
    $type = $_POST['type'];
    $rent = $_POST['daily_rate'];
    $buy = $_POST['price'];
    $desc = mysqli_real_escape_string($conn, $_POST['description']);
    
    $target_dir = "uploads/";
    $image_query_part = ""; 
    
    if (!empty($_FILES["car_image"]["name"])) {
        if (!is_dir($target_dir)) mkdir($target_dir, 0777, true);
        $image_name = time() . "_" . basename($_FILES["car_image"]["name"]);
        $target_file = $target_dir . $image_name;
        if (move_uploaded_file($_FILES["car_image"]["tmp_name"], $target_file)) {
            $image_query_part = ", image_path='$target_file'";
        }
    }

    if (isset($_POST['update_id']) && $_POST['update_id'] != 0) {
        $id = intval($_POST['update_id']);
        $sql = "UPDATE cars SET brand='$brand', model='$model', type='$type', daily_rate='$rent', price='$buy', description='$desc' $image_query_part WHERE id=$id";
    } else {
        if (isset($target_file)) {
            $sql = "INSERT INTO cars (brand, model, type, daily_rate, price, image_path, description) 
                    VALUES ('$brand', '$model', '$type', '$rent', '$buy', '$target_file', '$desc')";
        }
    }

    if (isset($sql) && mysqli_query($conn, $sql)) {
        header("Location: admin_add_car.php");
        exit();
    }
}

// --- REVENUE CALCULATIONS ---
$rent_res = mysqli_query($conn, "SELECT SUM(total_price) as total FROM bookings");
$total_rent = mysqli_fetch_assoc($rent_res)['total'] ?? 0;

$sale_res = mysqli_query($conn, "SELECT SUM(price) as total FROM sales");
$total_sale = mysqli_fetch_assoc($sale_res)['total'] ?? 0;

// --- SEARCH LOGIC (UPDATED) ---
$search_filter = "";
if (isset($_GET['search_btn']) && !empty($_GET['search_val'])) {
    $s = mysqli_real_escape_string($conn, $_GET['search_val']);
    $clean_id = str_replace('#', '', $s);
    // Updated WHERE clause: Adds 't' (Type) and ensures Name (u_name) and Phone (p) are searchable
    $search_filter = " WHERE car_id = '$clean_id' OR u_name LIKE '%$s%' OR t LIKE '%$s%' OR p LIKE '%$s%' ";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>REVS Admin | All-in-One</title>
    <style>
        body { background: #0a0a0a; color: white; font-family: 'Segoe UI', sans-serif; margin: 0; padding: 20px; }
        .tab-nav { display: flex; justify-content: center; gap: 15px; margin-bottom: 30px; border-bottom: 1px solid #222; padding-bottom: 20px; }
        .tab-btn { background: #151515; color: #888; border: 1px solid #333; padding: 12px 25px; border-radius: 5px; cursor: pointer; font-weight: bold; text-transform: uppercase; transition: 0.3s; }
        .tab-btn.active { background: #d62828; color: white; border-color: #d62828; }
        .tab-content { display: none; animation: fadeIn 0.3s ease; }
        .tab-content.active { display: block; }
        @keyframes fadeIn { from { opacity: 0; } to { opacity: 1; } }
        .main-wrapper { display: flex; gap: 30px; align-items: flex-start; }
        .form-box { background: #151515; padding: 25px; border-radius: 10px; width: 350px; border: 1px solid #333; }
        .table-box { flex: 1; background: #151515; padding: 25px; border-radius: 10px; border: 1px solid #333; }
        h2 span { color: #d62828; }
        input, select, textarea { width: 100%; padding: 10px; margin-bottom: 15px; background: #222; border: 1px solid #444; color: white; border-radius: 4px; box-sizing: border-box; }
        .btn-submit { background: #d62828; color: white; border: none; padding: 12px; width: 100%; font-weight: bold; cursor: pointer; }
        table { width: 100%; border-collapse: collapse; margin-top: 15px; }
        th { text-align: left; padding: 12px; color: #d62828; border-bottom: 2px solid #333; font-size: 11px; text-transform: uppercase; }
        td { padding: 12px; border-bottom: 1px solid #222; font-size: 13px; }
        .thumb { width: 50px; height: 30px; object-fit: cover; border-radius: 3px; }
        .stats-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 20px; margin-bottom: 30px; }
        .stat-card { background: #151515; padding: 20px; border-radius: 10px; border: 1px solid #222; text-align: center; }
        .stat-card p { font-size: 28px; font-weight: 900; color: #d62828; margin: 10px 0 0 0; }
        @media print { .tab-nav, .form-box, .btn-submit, .action-link, .search-container { display: none !important; } body { background: white; color: black; } }
    </style>
</head>
<body>

    <div class="tab-nav">
        <button class="tab-btn <?php echo ($edit_mode || !isset($_GET['search_btn'])) ? 'active' : ''; ?>" onclick="openTab(event, 'fleet')">Manage Fleet</button>
        <button class="tab-btn <?php echo (!$edit_mode && isset($_GET['search_btn'])) ? 'active' : ''; ?>" onclick="openTab(event, 'revenue')">Revenue & Reports</button>
        <a href="home.php" style="text-decoration:none; margin-left: auto;"><button class="tab-btn">View Website</button></a>
    </div>

    <div id="fleet" class="tab-content <?php echo ($edit_mode || !isset($_GET['search_btn'])) ? 'active' : ''; ?>">
        <div class="main-wrapper">
            <div class="form-box">
                <h2><?php echo $edit_mode ? "Edit" : "Add"; ?> <span>Vehicle</span></h2>
                <form method="POST" enctype="multipart/form-data">
                    <input type="hidden" name="update_id" value="<?php echo $update_id; ?>">
                    <select name="brand" required>
                        <option value="" disabled <?php if(!$brand) echo 'selected'; ?>>Select Brand</option>
                        <option value="BMW" <?php if($brand=='BMW') echo 'selected'; ?>>BMW</option>
                        <option value="TOYOTA" <?php if($brand=='TOYOTA') echo 'selected'; ?>>TOYOTA</option>
                        <option value="Honda" <?php if($brand=='Honda') echo 'selected'; ?>>Honda</option>
                        <option value="Mercedes-Benz" <?php if($brand=='Mercedes-Benz') echo 'selected'; ?>>Mercedes-Benz</option>
                        <option value="Porsche" <?php if($brand=='Porsche') echo 'selected'; ?>>Porsche</option>
                        <option value="GMC" <?php if($brand=='GMC') echo 'selected'; ?>>GMC</option>
                        <option value="Chevrolet" <?php if($brand=='Chevrolet') echo 'selected'; ?>>Chevrolet</option>
                        <option value="Isuzu" <?php if($brand=='Isuzu') echo 'selected'; ?>>Isuzu</option>
                        <option value="Hyundai" <?php if($brand=='Hyundai') echo 'selected'; ?>>Hyundai</option>
                        <option value="Ford" <?php if($brand=='Ford') echo 'selected'; ?>>Ford</option>
                        <option value="Iveco" <?php if($brand=='Iveco') echo 'selected'; ?>>Iveco</option>
                        <option value="Chrysler Pacifica" <?php if($brand=='Chrysler Pacifica') echo 'selected'; ?>>Chrysler Pacifica</option>
                        <option value="Rely/Chery" <?php if($brand=='Rely/Chery') echo 'selected'; ?>>Rely/Chery</option>
                    </select>
                    <input type="text" name="model" placeholder="Model" value="<?php echo $model; ?>" required>
                    <select name="type">
                        <option value="SUV" <?php if($type=='SUV') echo 'selected'; ?>>SUV</option>
                        <option value="Sedan" <?php if($type=='Sedan') echo 'selected'; ?>>Sedan</option>
                        <option value="Sport" <?php if($type=='Sport') echo 'selected'; ?>>Sport</option>
                        <option value="Truck" <?php if($type=='Truck') echo 'selected'; ?>>Truck</option>
                        <option value="Van" <?php if($type=='Van') echo 'selected'; ?>>Van</option>
                    </select>
                    <input type="number" name="daily_rate" placeholder="Rent Price" value="<?php echo $daily_rate; ?>" required>
                    <input type="number" name="price" placeholder="Selling Price" value="<?php echo $price; ?>" required>
                    <textarea name="description" placeholder="Short Description"><?php echo $desc; ?></textarea>
                    <input type="file" name="car_image" accept="image/*" <?php if(!$edit_mode) echo "required"; ?>>
                    <button type="submit" name="save_car" class="btn-submit"><?php echo $edit_mode ? "UPDATE VEHICLE" : "SAVE NEW CAR"; ?></button>
                </form>
            </div>
            <div class="table-box">
                <h2>Current <span>Inventory</span></h2>
                <table>
                    <thead><tr><th>Image</th><th>Car</th><th>Rate/Price</th><th>Actions</th></tr></thead>
                    <tbody>
                        <?php
                        $res = mysqli_query($conn, "SELECT * FROM cars ORDER BY id DESC");
                        while($row = mysqli_fetch_assoc($res)): ?>
                        <tr>
                            <td><img src="<?php echo $row['image_path']; ?>" class="thumb"></td>
                            <td><b><?php echo $row['brand']; ?></b> <?php echo $row['model']; ?></td>
                            <td>$<?php echo number_format($row['daily_rate']); ?> / $<?php echo number_format($row['price']); ?></td>
                            <td>
                                <a href="?edit=<?php echo $row['id']; ?>" style="color:#4CAF50; text-decoration:none; font-weight:bold; margin-right:10px;">Edit</a>
                                <a href="?delete=<?php echo $row['id']; ?>" style="color:#f44336; text-decoration:none; font-weight:bold;" onclick="return confirm('Delete this car?');">Delete</a>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div id="revenue" class="tab-content <?php echo (!$edit_mode && isset($_GET['search_btn'])) ? 'active' : ''; ?>">
        <div class="stats-grid">
            <div class="stat-card"><h3>Total Rental Revenue</h3><p>$<?php echo number_format($total_rent); ?></p></div>
            <div class="stat-card"><h3>Total Sales Revenue</h3><p>$<?php echo number_format($total_sale); ?></p></div>
            <div class="stat-card"><h3>Combined Net</h3><p style="color:#28a745;">$<?php echo number_format($total_rent + $total_sale); ?></p></div>
        </div>

        <div class="table-box">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
                <h2>Master <span>Transaction Log</span></h2>
                <button onclick="window.print()" class="tab-btn">Print Report</button>
            </div>

            <form method="GET" style="display: flex; gap: 10px; margin-bottom: 20px;" class="search-container">
                <input type="text" name="search_val" placeholder="Search by Name, Phone, Type, or Car ID (#)..." value="<?php echo isset($_GET['search_val']) ? htmlspecialchars($_GET['search_val']) : ''; ?>" style="margin-bottom: 0;">
                <button type="submit" name="search_btn" class="tab-btn" style="background: #333; border: none; color: white;">Search</button>
                <?php if(isset($_GET['search_btn'])): ?>
                    <a href="admin_add_car.php" class="tab-btn" style="text-decoration: none; background: #d62828; color: white; display: flex; align-items: center;">Clear</a>
                <?php endif; ?>
            </form>

            <table>
                <thead><tr><th>Date</th><th>Type</th><th>Car ID</th><th>Customer Name</th><th>Phone</th><th>Amount</th></tr></thead>
                <tbody>
                    <?php
                    // --- UPDATE: JOINING BY USER_ID ---
                    // Correctly fetching customer name using user_id from bookings/sales tables
                    $sql_union = "SELECT * FROM (
                        (SELECT b.booking_date as d, 'Rental' as t, b.car_id, b.phone as p, b.total_price as a, u.name as u_name 
                         FROM bookings b LEFT JOIN users u ON b.user_id = u.id) 
                        UNION 
                        (SELECT s.sale_date as d, 'Purchase' as t, s.car_id, s.phone as p, s.price as a, u.name as u_name 
                         FROM sales s LEFT JOIN users u ON s.user_id = u.id)
                    ) AS combined $search_filter ORDER BY d DESC";

                    $logs = mysqli_query($conn, $sql_union);
                    if ($logs && mysqli_num_rows($logs) > 0) {
                        while($row = mysqli_fetch_assoc($logs)): ?>
                        <tr>
                            <td><?php echo date('M d, Y', strtotime($row['d'])); ?></td>
                            <td><span style="color:<?php echo $row['t']=='Rental' ? '#007bff' : '#28a745'; ?>"><?php echo $row['t']; ?></span></td>
                            <td>#<?php echo $row['car_id']; ?></td>
                            <td><?php echo !empty($row['u_name']) ? htmlspecialchars($row['u_name']) : 'N/A'; ?></td>
                            <td><?php echo $row['p']; ?></td>
                            <td style="font-weight:bold;">$<?php echo number_format($row['a']); ?></td>
                        </tr>
                        <?php endwhile; 
                    } else {
                        echo "<tr><td colspan='6' style='text-align:center; padding: 20px; color:#666;'>No transactions found.</td></tr>";
                    } ?>
                </tbody>
            </table>
        </div>
    </div>

    <script>
        function openTab(evt, tabName) {
            var i, tabcontent, tablinks;
            tabcontent = document.getElementsByClassName("tab-content");
            for (i = 0; i < tabcontent.length; i++) { tabcontent[i].style.display = "none"; }
            tablinks = document.getElementsByClassName("tab-btn");
            for (i = 0; i < tablinks.length; i++) { tablinks[i].className = tablinks[i].className.replace(" active", ""); }
            document.getElementById(tabName).style.display = "block";
            evt.currentTarget.className += " active";
        }
    </script>
</body>
</html>