<?php
session_start();
include 'db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    if (isset($_POST['username']) && isset($_POST['password'])) {

        $username = trim($_POST['username']);
        $password = trim($_POST['password']);

        $stmt = $conn->prepare("SELECT id, username, password FROM users WHERE username = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows == 1) {

            $stmt->bind_result($id, $db_username, $db_password);
            $stmt->fetch();

            if ($password === $db_password) {

                $_SESSION['user_id'] = $id;
                $_SESSION['username'] = $db_username;

                header("Location: /DineFlow/customer/dashboard.php");
                exit();

            } else {
                header("Location: /DineFlow/login.php?error=InvalidCredentials");
                exit();
            }

        } else {
            header("Location: /DineFlow/login.php?error=InvalidCredentials");
            exit();
        }

        $stmt->close();
    }
}

$conn->close();
?>
