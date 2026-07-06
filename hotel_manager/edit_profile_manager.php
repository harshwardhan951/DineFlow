<?php
session_start();
include '../db.php';

if (!isset($_SESSION['hotel_id'])) {
    header("Location: ../login.php");
    exit();
}

$hotel_id = $_SESSION['hotel_id'];
$message = "";

// Fetch manager & hotel details
$stmt = $conn->prepare("SELECT * FROM managers WHERE hotel_id = ?");
$stmt->bind_param("i", $hotel_id);
$stmt->execute();
$manager_result = $stmt->get_result();
$manager = $manager_result->fetch_assoc();

$stmt2 = $conn->prepare("SELECT * FROM hotels WHERE id = ?");
$stmt2->bind_param("i", $hotel_id);
$stmt2->execute();
$hotel_result = $stmt2->get_result();
$hotel = $hotel_result->fetch_assoc();

// Handle form submission
if (isset($_POST['update_profile'])) {

    $new_username = $_POST['username'];

    // Image upload
    $image_name = $hotel['image']; // default current image
    if (isset($_FILES['image']) && $_FILES['image']['name'] != '') {
        $tmp_name = $_FILES['image']['tmp_name'];
        $image_name = time() . "_" . $_FILES['image']['name'];
        $upload_path = "../uploads/" . $image_name;
        move_uploaded_file($tmp_name, $upload_path);
    }

    // Update managers table (username)
    $stmt = $conn->prepare("UPDATE managers SET username=? WHERE hotel_id=?");
    $stmt->bind_param("si", $new_username, $hotel_id);
    $stmt->execute();

    // Update hotels table (manager_username + image)
    $stmt2 = $conn->prepare("UPDATE hotels SET manager_username=?, image=? WHERE id=?");
    $stmt2->bind_param("ssi", $new_username, $image_name, $hotel_id);
    $stmt2->execute();

    $message = "Profile updated successfully!";
    // Refresh data
    header("Location: edit_profile_manager.php?success=1");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Edit Profile</title>
<style>
* { margin:0; padding:0; box-sizing:border-box; font-family:'Segoe UI', sans-serif; }
body { background:#0b1220; color:#e5e7eb; }
.layout { display:flex; }
/* Sidebar */
.sidebar {
    width:240px; height:100vh; background:#020617; padding:20px; position:fixed;
    box-shadow: 2px 0 10px rgba(0,0,0,0.5);
}
.sidebar h2 { text-align:center; color:#38bdf8; margin-bottom:40px; font-size:22px; }
.sidebar a { display:block; padding:14px 16px; margin-bottom:12px; color:#cbd5f5; text-decoration:none; border-radius:10px; font-size:15px; }
.sidebar a:hover, .sidebar a.active { background:#1e293b; color:#38bdf8; }
/* Main Content */
.content { margin-left:240px; width:100%; padding:40px 50px; max-width:900px; }
/* Page Header */
.page-header { margin-bottom:30px; }
.page-header h1 { font-size:28px; margin-bottom:6px; }
.page-header p { color:#94a3b8; }
/* Card */
.card {
    background:#0f172a; border-radius:16px; padding:28px; margin-bottom:40px;
    box-shadow:0 10px 30px rgba(0,0,0,0.6); border:1px solid #1e293b;
    display:flex; flex-direction: column; align-items: center;
}
/* Profile Image */
.profile-img {
    width:130px; height:130px; border-radius:50%; object-fit:cover;
    border:3px solid #38bdf8; margin-bottom:20px;
}
/* Form */
form { width:100%; display:flex; flex-direction: column; align-items: center; }
form input[type="text"], form input[type="file"] {
    width:100%; max-width:400px; margin-bottom:16px; padding:12px; border-radius:10px;
    border:1px solid #1e293b; background:#020617; color:#fff; font-size:14px;
}
form input:focus { outline:none; border-color:#38bdf8; }
button { background:#38bdf8; color:#020617; font-weight:600; padding:12px 24px; border:none; border-radius:12px; cursor:pointer; transition:0.3s; }
button:hover { background:#0ea5e9; }
.message { margin-bottom:16px; color:#16a34a; font-weight:600; }
@media(max-width:768px){ .content { padding:30px 20px; } .card { padding:20px; } }
</style>
</head>
<body>

<div class="layout">

    <div class="sidebar">
        <h2>DineFlow</h2>
        <a href="manager_dashboard.php">🏠 Dashboard</a>
        <a href="view_order.php">📦 View Orders</a>
        <a href="manager_menu.php">🍽 Manage Menu</a>
        <a href="manager_profile.php">👤 Profile</a>
        <a href="../logout.php">🚪 Logout</a>
    </div>

    <div class="content">
        <div class="page-header">
            <h1>✏️ Edit Profile</h1>
            <p>Update your account details</p>
        </div>

        <div class="card">
            <?php if(isset($_GET['success'])): ?>
                <div class="message">Profile updated successfully!</div>
            <?php endif; ?>

            <!-- Profile Image -->
            <img src="../uploads/<?php echo $hotel['image'] ?? 'default.png'; ?>" alt="Profile Image" class="profile-img">

            <form method="POST" enctype="multipart/form-data">
                <input type="text" name="username" value="<?php echo $manager['username'] ?? ''; ?>" placeholder="Username" required>
                <input type="file" name="image" accept="image/*">
                <button type="submit" name="update_profile">💾 Save Changes</button>
            </form>
        </div>
    </div>

</div>

</body>
</html>
