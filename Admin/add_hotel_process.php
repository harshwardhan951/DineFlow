include '../db.php';

$hotel_name = $_POST['hotel_name'];
$location   = $_POST['location'];
$status     = $_POST['status'];

$manager_username = $_POST['manager_username'];
$manager_password = password_hash($_POST['manager_password'], PASSWORD_DEFAULT);

// insert hotel
mysqli_query($conn, "
INSERT INTO hotels (hotel_name, location, status)
VALUES ('$hotel_name','$location','$status')
");

$hotel_id = mysqli_insert_id($conn);

// create manager user
mysqli_query($conn, "
INSERT INTO users (username, password, role, hotel_id)
VALUES ('$manager_username','$manager_password','manager',$hotel_id)
");

header("Location: view_hotels.php?success=added");
