<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header("Location: admin_login.php");
    exit;
}

include '../db.php';
$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") 
{
    $hotel_name = trim($_POST['hotel_name']);
    $location = trim($_POST['location']);
    $status = $_POST['status'];

    // Manager details
    $manager_name = isset($_POST['manager_name']) ? trim($_POST['manager_name']) : '';
    $manager_email = isset($_POST['manager_email']) ? trim($_POST['manager_email']) : '';
    $manager_password = isset($_POST['manager_password']) ? trim($_POST['manager_password']) : '';

    // Validate manager fields
    if (empty($manager_name) || empty($manager_email) || empty($manager_password)) {
        $message = "Please fill all manager details!";
    } elseif (!filter_var($manager_email, FILTER_VALIDATE_EMAIL)) {
        $message = "Please enter a valid email for manager!";
    } else {
        // Handle image upload
        if(isset($_FILES['image']) && $_FILES['image']['name'] != "") {
            $image_name = time() . '_' . $_FILES['image']['name'];
            $target = "../uploads/" . $image_name;

            if(move_uploaded_file($_FILES['image']['tmp_name'], $target)) {
                // Insert hotel into DB
                $stmt = $conn->prepare("INSERT INTO hotels (hotel_name, location, image, status) VALUES (?, ?, ?, ?)");
                $stmt->bind_param("ssss", $hotel_name, $location, $image_name, $status);

                if($stmt->execute()) {
                    $hotel_id = $conn->insert_id;

                    // Check if manager email already exists
                    $check_email = $conn->prepare("SELECT id FROM managers WHERE email=?");
                    $check_email->bind_param("s", $manager_email);
                    $check_email->execute();
                    $check_email->store_result();

                    if ($check_email->num_rows > 0) {
                        $message = "Manager email already exists!";
                        unlink($target); // delete uploaded hotel image
                    } else {
                        // Insert manager
                        $stmt2 = $conn->prepare("INSERT INTO managers (hotel_id, name, email, password) VALUES (?, ?, ?, ?)");
                        $stmt2->bind_param("isss", $hotel_id, $manager_name, $manager_email, $manager_password);

                        if($stmt2->execute()) {
                            $message = "Hotel & Manager added successfully!";
                        } else {
                            $message = "Hotel added but failed to add manager: " . $stmt2->error;
                        }
                        $stmt2->close();
                    }

                    $check_email->close();
                } else {
                    $message = "Database error: " . $stmt->error;
                }

                $stmt->close();
            } else {
                $message = "Failed to upload image!";
            }
        } else {
            $message = "Please select an image!";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Add Hotel | DineFlow Admin</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&display=swap" rel="stylesheet">
<style>
* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
    font-family: 'Poppins', sans-serif;
}

body {
    display: flex;
    min-height: 100vh;
    background: #0f172a;
    color: #e5e7eb;
}

/* SIDEBAR */
.sidebar {
    width: 240px;
    background: #020617;
    padding: 30px 20px;
    display: flex;
    flex-direction: column;
    position: fixed;
    height: 100%;
}

.sidebar h2 {
    color: #38bdf8;
    text-align: center;
    margin-bottom: 40px;
}

.sidebar ul {
    list-style: none;
    padding: 0;
}

.sidebar ul li {
    margin-bottom: 20px;
}

.sidebar ul li a {
    display: block;
    color: #cbd5f5;
    text-decoration: none;
    padding: 12px 15px;
    border-radius: 8px;
    transition: 0.3s;
}

.sidebar ul li a:hover,
.sidebar ul li a.active {
    background: #1e293b;
    color: #38bdf8;
}

/* MAIN CONTENT */
.main-content {
    margin-left: 240px; /* sidebar width */
    flex: 1;
    display: flex;
    justify-content: center;
    align-items: center;
    padding: 40px 20px;
}

/* CONTAINER BOX */
.container {
    background: #020617;
    padding: 40px 35px;
    border-radius: 20px;
    box-shadow: 0 20px 50px rgba(0,0,0,0.7);
    width: 100%;
    max-width: 500px;
    text-align: center;
}

.container h2 {
    color: #38bdf8;
    margin-bottom: 25px;
    font-size: 2rem;
}

/* FORM INPUTS */
form input,
form select,
form button {
    width: 100%;
    padding: 12px 15px;
    margin: 12px 0;
    border-radius: 10px;
    border: 1px solid #3b82f6;
    background: #0f172a;
    color: #e5e7eb;
    font-size: 1rem;
    transition: all 0.3s ease;
}

form input:focus,
form select:focus {
    outline: none;
    border-color: #38bdf8;
    box-shadow: 0 0 10px #38bdf8;
}

/* BUTTON */
form button {
    background: #38bdf8;
    border: none;
    color: #020617;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s ease;
}

form button:hover {
    background: #0ea5e9;
    transform: scale(1.05);
}

/* IMAGE PREVIEW */
#preview {
    width: 100%;
    margin-top: 10px;
    border-radius: 10px;
    max-height: 200px;
    object-fit: cover;
    display: none;
}

/* MESSAGE BOX */
.message {
    padding: 10px;
    margin-bottom: 15px;
    background: #38bdf8;
    color: #020617;
    border-radius: 8px;
    font-weight: 500;
}

/* RESPONSIVE */
@media(max-width: 768px){
    .sidebar {
        position: relative;
        width: 100%;
        height: auto;
        padding: 20px;
    }
    .main-content {
        margin-left: 0;
        padding: 20px;
        align-items: flex-start;
    }
}
</style>
</head>
<body>

<!-- SIDEBAR -->
<div class="sidebar">
    <h2>DineFlow Admin</h2>
    <ul>
        <li><a href="admin_dashboard.php">🏠 Dashboard</a></li>
        <li><a class="active" href="add_hotel.php">➕ Add Hotel</a></li>
        <li><a href="view_hotels.php">🏨 View Hotels</a></li>
        <li><a href="view_orders.php">📦 View Orders</a></li>
        <li><a href="../logout.php">🔒 Logout</a></li>
    </ul>
</div>


<!-- MAIN CONTENT -->
<div class="main-content">
    <div class="container">
        <h2>Add New Hotel</h2>
        <?php if($message != "") echo "<div class='message'>$message</div>"; ?>
        <form method="post" enctype="multipart/form-data">
            <input type="text" name="hotel_name" placeholder="Hotel Name" required maxlength="50">
            <input type="text" name="location" placeholder="Location" required maxlength="100">
            <input type="file" name="image" accept="image/*" id="image" onchange="previewImage(event)" required>
            <img id="preview" alt="Image Preview">
            <select name="status" required>
                <option value="">Select Status</option>
                <option value="active">Active</option>
                <option value="inactive">Inactive</option>
            </select>
            <h3>Manager Details</h3>
                
                <input type="text" name="manager_name" placeholder="Manager Name" required>
                <input type="email" name="manager_email" placeholder="Manager Email" required>
                <input type="password" name="manager_password" placeholder="Manager Password" required>

            <button type="submit">Add Hotel</button>
        </form>
    </div>
</div>

<script>
function previewImage(event) {
    const preview = document.getElementById('preview');
    preview.src = URL.createObjectURL(event.target.files[0]);
    preview.style.display = 'block';
}
</script>

</body>
</html>
