<?php
session_start();
include 'db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $username = trim($_POST['username']);
    $email = trim($_POST['email']); // not stored (table has no email column)
    $password = trim($_POST['password']);
    $confirm_password = trim($_POST['confirm_password']);

    // Check passwords match
    if ($password !== $confirm_password) {
        header("Location: register.php?error=PasswordsDoNotMatch");
        exit();
    }

    // Check if username already exists
    $checkStmt = $conn->prepare("SELECT id FROM users WHERE username = ?");
    $checkStmt->bind_param("s", $username);
    $checkStmt->execute();
    $checkStmt->store_result();

    if ($checkStmt->num_rows > 0) {
        header("Location: register.php?error=UsernameOrEmailExists");
        exit();
    }

    $checkStmt->close();

    // Insert new user
    $stmt = $conn->prepare(
    "INSERT INTO users (username, email, password) VALUES (?, ?, ?)"
);

$stmt->bind_param("sss", $username, $email, $password);

    if ($stmt->execute()) {
        header("Location: register.php?success=RegistrationComplete");
        exit();
    } else {
        header("Location: register.php?error=RegistrationFailed");
        exit();
    }

    $stmt->close();
}

$conn->close();
?>
