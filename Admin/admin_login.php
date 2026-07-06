<?php
session_start();
include '../db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST['username']);
    $password = $_POST['password'];

    $stmt = $conn->prepare("SELECT id, username, password, role FROM users WHERE username=?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows == 1) {
        $stmt->bind_result($id, $db_username, $db_password, $role);
        $stmt->fetch();

        if ($password === $db_password && $role === 'admin') {
            $_SESSION['user_id'] = $id;
            $_SESSION['username'] = $db_username;
            $_SESSION['role'] = $role;
            header("Location: admin_dashboard.php"); // admin dashboard
            exit;
        } else {
            $error = "Invalid credentials or not an admin!";
        }
    } else {
        $error = "Invalid credentials!";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Admin Login | DineFlow</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&display=swap" rel="stylesheet">
<style>
body {
    font-family: 'Poppins', sans-serif;
    background: linear-gradient(135deg, #1e293b, #0f172a);
    height: 100vh;
    display: flex;
    justify-content: center;
    align-items: center;
    margin: 0;
}

.login-container {
    background: #020617;
    padding: 40px 35px;
    border-radius: 15px;
    box-shadow: 0 15px 30px rgba(0,0,0,0.6);
    width: 100%;
    max-width: 400px;
    color: #e5e7eb;
    text-align: center;
    position: relative;
}

.login-container h2 {
    margin-bottom: 25px;
    color: #38bdf8;
    font-weight: 600;
    font-size: 1.8rem;
}

.login-container input {
    width: 100%;
    padding: 12px 15px;
    margin: 12px 0;
    border-radius: 8px;
    border: none;
    background: #0f172a;
    color: #e5e7eb;
    border: 1px solid #3b82f6;
    transition: 0.3s;
}

.login-container input:focus {
    outline: none;
    border-color: #38bdf8;
    box-shadow: 0 0 5px #38bdf8;
}

.login-container button {
    width: 100%;
    padding: 12px;
    margin-top: 15px;
    border: none;
    border-radius: 8px;
    background: #38bdf8;
    color: #020617;
    font-weight: 600;
    cursor: pointer;
    transition: 0.3s;
}

.login-container button:hover {
    background: #0ea5e9;
    transform: scale(1.03);
}

.error {
    background: #f87171;
    color: #020617;
    padding: 8px 10px;
    margin-bottom: 15px;
    border-radius: 6px;
    font-weight: 500;
    font-size: 0.9rem;
}

.forgot {
    display: block;
    margin-top: 10px;
    font-size: 0.9rem;
    color: #94a3b8;
    text-decoration: none;
}

.forgot:hover {
    color: #38bdf8;
}
</style>
</head>
<body>

<div class="login-container">
    <h2>Admin Login</h2>
    <?php if(isset($error)) echo "<div class='error'>$error</div>"; ?>
    <form method="post">
        <input type="text" name="username" placeholder="Username" required maxlength="50">
        <input type="password" name="password" placeholder="Password" required maxlength="50">
        <button type="submit">Login</button>
        <a href="../login.php" class="forgot">Customer Login</a>
    </form>
</div>

</body>
</html>
