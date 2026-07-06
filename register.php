<?php
// Display inline messages
$message = '';
if(isset($_GET['error'])){
    if($_GET['error'] == "PasswordsDoNotMatch") $message = "Passwords do not match!";
    if($_GET['error'] == "UsernameOrEmailExists") $message = "Username or Email already exists!";
    if($_GET['error'] == "RegistrationFailed") $message = "Registration failed. Try again!";
}
if(isset($_GET['success'])){
    if($_GET['success'] == "RegistrationComplete") $message = "Registration successful! Please login.";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>DineFlow – Register</title>
<style>
*{margin:0;padding:0;box-sizing:border-box;font-family:'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;}
html, body{height:100%; overflow:hidden; background:#0f172a; position:relative;}
body::before{content:""; position:absolute; top:0; left:0; width:100%; height:100%; background-color: rgba(0,0,0,0.7); z-index:0;}
.register-container{position: relative; z-index:1; height:100%; display:flex; justify-content:center; align-items:center;}
.register-box{
    background: #1e293b;
    padding:40px 30px;
    border-radius:15px;
    box-shadow:0 15px 30px rgba(0,0,0,0.5);
    width:400px;
    max-width:90%;
    text-align:center;
    transition: transform 0.3s ease, box-shadow 0.3s ease;
}
.register-box:hover{
    transform: translateY(-5px);
    box-shadow:0 20px 40px rgba(0,0,0,0.7);
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

h2{color: #38bdf8; margin-bottom:25px;}
input{
    width:100%;
    padding:12px 15px;
    margin:10px 0;
    border-radius:8px;
    border:1px solid #334155;
    background:#0f172a;
    color:#e5e7eb;
    font-size:1rem;
    transition: all 0.3s ease;
}
input:focus{
    border-color:#38bdf8;
    outline:none;
    box-shadow:0 0 8px rgba(56,189,248,0.4);
}
.top-home{
    position:absolute;
    top:20px;
    left:30px;
}

.logo-link{
    display:flex;
    align-items:center;
    text-decoration:none;
    font-size:22px;
    font-weight:bold;
    color:#38bdf8;
}

.logo-link img{
    width:40px;
    height:40px;
    margin-right:10px;
}

.logo-link:hover{
    opacity:0.8;
}
button{
    width:100%;
    padding:12px;
    border-radius:25px;
    border:none;
    background:#38bdf8;
    color:#020617;
    font-size:1rem;
    cursor:pointer;
    margin-top:15px;
    transition: all 0.3s ease;
}
button:hover{
    background:#0ea5e9;
    transform: scale(1.05);
}
p{margin-top:15px; font-size:0.9rem; color:#cbd5f1;}
p a{color:#38bdf8; text-decoration:none;}
p a:hover{text-decoration:underline;}
.message{margin-bottom:15px; color:#f87171; font-weight:bold;}
.success{color:#4ade80;}
@media(max-width:480px){.register-box{padding:30px 20px;}}

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

<div class="register-container">
    <div class="register-box">
         <div class="login-logo">
            <img src="assets/Images/dine.jpg" alt="DineFlow">
            <h2>DineFlow</h2>
        </div>
        <?php if($message){ 
            $class = (isset($_GET['success'])) ? 'success' : 'message';
            echo "<p class='$class'>$message</p>"; 
        } ?>
        <form action="register_process.php" method="POST">
            <input type="text" name="username" placeholder="Username" required>
            <input type="email" name="email" placeholder="Email" required>
            <input type="password" name="password" placeholder="Password" required>
            <input type="password" name="confirm_password" placeholder="Confirm Password" required>
            <button type="submit">Register</button>
            <p>Already have an account? <a href="login.php">Login</a></p>
        </form>
    </div>
</div>

</body>
</html>
