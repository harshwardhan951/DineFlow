<?php
session_start();
include 'db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    // Check users table
    $stmt = $conn->prepare("SELECT id, username, password, role FROM users WHERE username=?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows == 1) {

        $stmt->bind_result($id, $db_username, $db_password, $role);
        $stmt->fetch();

        if ($password === $db_password) {

            $_SESSION['user_id'] = $id;
            $_SESSION['username'] = $db_username;
            $_SESSION['role'] = $role;

            if ($role === 'admin') {
                header("Location: Admin/admin_dashboard.php");
            } else {
                header("Location: customer/dashboard.php");
            }
            exit;

        } else {
            echo "Invalid password!";
        }

    } else {

        // Check managers table
     // Check managers table
$stmt2 = $conn->prepare("SELECT id, name, password, hotel_id FROM managers WHERE name=?");
$stmt2->bind_param("s", $username);
$stmt2->execute();
$stmt2->store_result();

if ($stmt2->num_rows == 1) {

    $stmt2->bind_result($id, $db_name, $db_password, $hotel_id);
    $stmt2->fetch();

    if ($password === $db_password) {
        $_SESSION['user_id'] = $id;
        $_SESSION['username'] = $db_name;  // store manager name in session
        $_SESSION['role'] = 'manager';
        $_SESSION['hotel_id'] = $hotel_id;

        header("Location: hotel_manager/manager_dashboard.php");
        exit;
    } else {
        $error = "Invalid password!";
    }

} else {
    $error = "Invalid username!";
}

    $stmt2->close();
    }

    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>DineFlow Login</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&display=swap" rel="stylesheet">

<style>
*{
    margin:0;
    padding:0;
    box-sizing:border-box;
    font-family:'Poppins',sans-serif;
}

/* ===== PROFESSIONAL BACKGROUND ===== */
body{
    min-height:100vh;
    background:
        linear-gradient(180deg,#020617,#020617),
        radial-gradient(circle at top left, rgba(56,189,248,0.12), transparent 45%),
        radial-gradient(circle at bottom right, rgba(99,102,241,0.12), transparent 45%);
    display:flex;
    justify-content:center;
    align-items:center;
    color:#e5e7eb;
}

/* subtle texture */
body::after{
    content:"";
    position:fixed;
    inset:0;
    background-image:url("https://www.transparenttextures.com/patterns/cubes.png");
    opacity:0.04;
    pointer-events:none;
}

/* ===== LOGIN CARD ===== */
.login-container{
    background:#1e293b;
    padding:40px 35px;
    width:100%;
    max-width:400px;
    border-radius:16px;
    text-align:center;
    border:1px solid rgba(255,255,255,0.06);
    box-shadow:
        0 25px 50px rgba(0,0,0,0.7),
        0 0 0 1px rgba(255,255,255,0.03);
}

.login-container h2{
    color:#38bdf8;
    font-size:1.8rem;
    margin-bottom:25px;
    font-weight:600;
}

/* ===== LOGO SECTION ===== */
.login-logo{
    display:flex;
    align-items:center;
    justify-content:center;
    gap:10px;
    margin-bottom:20px;
}

.login-logo img{
    width:38px;
    height:38px;
    object-fit:cover;
    border-radius:8px;
}

.login-logo h2{
    font-size:1.7rem;
    font-weight:600;
    color:#38bdf8;
    margin:0;
}

/* ===== INPUTS ===== */
.login-container input{
    width:100%;
    padding:13px 15px;
    margin:12px 0;
    border-radius:10px;
    border:1px solid #1e3a8a;
    background:#020617;
    color:#e5e7eb;
    font-size:0.95rem;
    transition:0.3s;
}

.login-container input::placeholder{
    color:#94a3b8;
}

.login-container input:focus{
    outline:none;
    border-color:#38bdf8;
    box-shadow:0 0 8px rgba(56,189,248,0.4);
}

/* ===== BUTTON ===== */
.login-container button{
    width:100%;
    padding:13px;
    margin-top:18px;
    border:none;
    border-radius:10px;
    background:linear-gradient(135deg,#38bdf8,#0ea5e9);
    color:#020617;
    font-weight:600;
    font-size:1rem;
    cursor:pointer;
    transition:0.3s;
}

.login-container button:hover{
    transform:translateY(-2px);
    box-shadow:0 10px 20px rgba(56,189,248,0.35);
}

/* ===== ERROR ===== */
.error{
    background:rgba(248,113,113,0.15);
    color:#f87171;
    padding:10px;
    margin-bottom:15px;
    border-radius:8px;
    font-size:0.9rem;
}



/* ===== LINK ===== */
.forgot{
    display:block;
    margin-top:15px;
    font-size:0.9rem;
    color:#94a3b8;
    text-decoration:none;
}

.forgot:hover{
    color:#38bdf8;
}

.login-footer p{
    display: flex;
    justify-content: center;
    align-items: center;
    gap: 6px;
    margin-top: 16px;
    font-size: 0.9rem;
    color: #9ca3af;
}

.login-footer a{
    color: #38bdf8;
    text-decoration: none;
    font-weight: 500;
}

.login-footer a:hover{
    text-decoration: underline;
}

/* ===== TOP RIGHT HOME BUTTON ===== */
.home-btn{
    position:fixed;
    top:20px;
    right:30px;
    padding:8px 16px;
    background:#38bdf8;
    color:#020617;
    text-decoration:none;
    font-weight:500;
    border-radius:8px;
    font-size:0.9rem;
    transition:0.3s;
    z-index:1000;
}

.home-btn:hover{
    background:#0ea5e9;
    transform:translateY(-2px);
}

</style>
</head>

<body>

<a href="/DineFlow/index.html" class="home-btn">
    🏠 Home
</a>

<div class="login-container">
    <div class="login-logo">
    <img src="assets/Images/dine.jpg" alt="DineFlow">
    <h2>DineFlow</h2>
</div>


    <?php if(isset($error)) echo "<div class='error'>$error</div>"; ?>

    <form method="post">
        <input type="text" name="username" placeholder="Username" required>
        <input type="password" name="password" placeholder="Password" required>
        <button type="submit">Login</button>
    </form>
    <div class="login-footer">
        <p>New User? <a href="register.php">Register Here</a></p>
    </div>

</div>

</body>
</html>

