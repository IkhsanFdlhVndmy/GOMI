<?php
session_start();
include "config.php";

if ($_SERVER['REQUEST_METHOD'] == "POST") {
    $email = $_POST['email'];
    $password = $_POST['password'];

    if ($email === "admin@gmail.com" && $password === "admin123") {
        $_SESSION['admin'] = true;
        header("Location: views/admin.php");
        exit();
    }

    $sql = "SELECT * FROM data_user WHERE email = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();

    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_name'] = $user['nama'];
        $_SESSION['user_point'] = $user['point'];
        header("Location: views/user.php");
        exit();
    } else {
        $_SESSION['error'] = "Email atau password salah!";
        header("Location: views/signin-signup.php");
        exit();
    }
}
?>
