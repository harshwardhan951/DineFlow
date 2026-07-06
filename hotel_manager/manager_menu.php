<?php
session_start();
include '../db.php';

if (!isset($_SESSION['hotel_id'])) {
    header("Location: ../login.php");
    exit();
}

$hotel_id = $_SESSION['hotel_id'];

/* ===== ADD MENU ITEM ===== */
if (isset($_POST['add_item'])) {

    $item_name   = $_POST['item_name'];
    $description = $_POST['description'];
    $price       = $_POST['price'];
    $category    = $_POST['category'];
    $status      = $_POST['status'];

    // Image upload
    $image_name = $_FILES['image']['name'];
    $tmp_name   = $_FILES['image']['tmp_name'];

    $upload_path = "../uploads/" . time() . "_" . $image_name;
    move_uploaded_file($tmp_name, $upload_path);

    $stmt = $conn->prepare(
        "INSERT INTO menu (hotel_id, item_name, description, price, category, image, status)
         VALUES (?, ?, ?, ?, ?, ?, ?)"
    );

    $stmt->bind_param(
        "issdsss",
        $hotel_id,
        $item_name,
        $description,
        $price,
        $category,
        $upload_path,
        $status
    );

    $stmt->execute();
    $stmt->close();
}

/* ===== DELETE MENU ITEM ===== */
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    $conn->query("DELETE FROM menu WHERE id=$id AND hotel_id=$hotel_id");
}

/* ===== FETCH MENU ITEMS ===== */
$result = $conn->query("SELECT * FROM menu WHERE hotel_id=$hotel_id ORDER BY id DESC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Manager | Menu</title>
<style>
/* Reset & Font */
* { margin:0; padding:0; box-sizing:border-box; font-family:'Segoe UI', sans-serif; }
body { background:#0b1220; color:#e5e7eb; }

/* Layout */
.layout { display:flex; }

/* Sidebar */
.sidebar {
    width:240px; height:100vh; background:#020617; padding:20px; position:fixed;
    box-shadow: 2px 0 10px rgba(0,0,0,0.5);
}
.sidebar h2 { text-align:center; color:#38bdf8; margin-bottom:40px; font-size:22px; }
.sidebar a {
    display:block; padding:14px 16px; margin-bottom:12px; color:#cbd5f5;
    text-decoration:none; border-radius:10px; font-size:15px;
}
.sidebar a:hover, .sidebar a.active { background:#1e293b; color:#38bdf8; }

/* Main Content */
.content { margin-left:240px; width:100%; padding:40px 50px; max-width:1200px; }

/* Page Header */
.page-header { margin-bottom:30px; }
.page-header h1 { font-size:28px; margin-bottom:6px; }
.page-header p { color:#94a3b8; }

/* Cards */
.card {
    background:#0f172a; border-radius:16px; padding:28px; margin-bottom:40px;
    box-shadow:0 10px 30px rgba(0,0,0,0.6);
    border:1px solid #1e293b;
}

/* Form Grid */
.form-grid { display:grid; grid-template-columns: repeat(2, 1fr); gap:20px; }
.form-grid textarea { grid-column: span 2; resize:none; height:80px; }
.form-grid button { grid-column: span 2; }

/* Inputs */
input, textarea, select {
    background:#1e293b; border:1px solid #334155; color:#fff; padding:12px;
    border-radius:10px; font-size:14px; transition:0.3s;
}
input:focus, textarea:focus, select:focus { outline:none; border-color:#38bdf8; }

/* Buttons */
button {
    background:#38bdf8; color:#020617; font-weight:600; cursor:pointer;
    padding:14px; border:none; border-radius:12px; transition:0.3s;
}
button:hover { background:#0ea5e9; }

/* Table */
.table-wrapper { overflow-y:auto; max-height:420px; border-radius:12px; }
table { width:100%; border-collapse:collapse; }
th, td { padding:16px; text-align:left; vertical-align: middle; }
th { position:sticky; top:0; background:#0f172a; color:#38bdf8; border-bottom:1px solid #334155; }
td { border-bottom:1px solid #334155; }
tr:hover { background:#1e293b; }

/* Images */
img { width:70px; height:50px; object-fit:cover; border-radius:8px; }

/* Status Badge */
.status { padding:6px 12px; border-radius:20px; font-size:13px; font-weight:600; display:inline-block; }
.status.Available { background:#16a34a; color:#fff; }
.status.OutOfStock { background:#dc2626; color:#fff; }

/* Delete Link */
.delete { color:#ef4444; text-decoration:none; font-weight:600; transition:0.3s; }
.delete:hover { text-decoration:underline; }
</style>
</head>
<body>

<div class="layout">

    <!-- Sidebar -->
    <div class="sidebar">
        <h2>DineFlow</h2>
        <a href="manager_dashboard.php">🏠 Dashboard</a>
        <a href="view_order.php">📦 View Orders</a>
        <a href="manager_menu.php" class="active">🍽 Manage Menu</a>
        <a href="manager_profile.php">👤 Profile</a>
        <a href="../logout.php">🚪 Logout</a>
    </div>

    <!-- Main Content -->
    <div class="content">

        <div class="page-header">
            <h1>🍽 Menu Management</h1>
            <p>Add, manage and control restaurant items</p>
        </div>

        <!-- Add Menu Item Card -->
        <div class="card">
            <h2 style="margin-bottom:20px;">➕ Add New Item</h2>
            <form method="POST" enctype="multipart/form-data" class="form-grid">
                <input type="text" name="item_name" placeholder="Item Name" required>
                <input type="number" step="0.01" name="price" placeholder="Price" required>
                <textarea name="description" placeholder="Description" required></textarea>
                <input type="text" name="category" placeholder="Category (Veg / Non-Veg / Drinks)" required>
                <select name="status">
                    <option value="Available">Available</option>
                    <option value="OutOfStock">Out of Stock</option>
                </select>
                <input type="file" name="image" required>
                <button type="submit" name="add_item">Add Menu Item</button>
            </form>
        </div>

        <!-- Menu List Card -->
        <div class="card">
            <h2 style="margin-bottom:20px;">📋 Menu Items</h2>
            <div class="table-wrapper">
                <table>
                    <tr>
                        <th>Image</th>
                        <th>Name</th>
                        <th>Category</th>
                        <th>Price</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                    <?php while($row = $result->fetch_assoc()) { ?>
                    <tr>
                        <td><img src="<?php echo $row['image']; ?>"></td>
                        <td><?php echo $row['item_name']; ?></td>
                        <td><?php echo $row['category']; ?></td>
                        <td>₹<?php echo number_format($row['price'],2); ?></td>
                        <td>
                            <span class="status <?php echo ($row['status']=='Available') ? 'Available' : 'OutOfStock'; ?>">
                                <?php echo ($row['status']=='Available') ? 'Available' : 'Out of Stock'; ?>
                            </span>
                        </td>
                        <td>
                            <a class="delete" href="?delete=<?php echo $row['id']; ?>">Delete</a>
                        </td>
                    </tr>
                    <?php } ?>
                </table>
            </div>
        </div>

    </div>

</div>

</body>
</html>
