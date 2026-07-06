session_start();
include '../db.php';

if($_SESSION['hotel_id'] != $hotel_id_from_url){
    die("Unauthorized Access");
}


$username = $_POST['username'];
$password = $_POST['password'];

$result = mysqli_query($conn, "
SELECT * FROM users 
WHERE username='$username' AND role='manager'
");

if(mysqli_num_rows($result) == 1){
    $user = mysqli_fetch_assoc($result);

    if(password_verify($password, $user['password'])){
        $_SESSION['user_id']  = $user['id'];
        $_SESSION['role']     = 'manager';
        $_SESSION['hotel_id']= $user['hotel_id'];

        header("Location: manager/dashboard.php");
        exit;
    }
}

header("Location: manager_login.php?error=invalid");
