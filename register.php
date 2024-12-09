<?php
session_start();
include "../config.php";

if ($_SERVER['REQUEST_METHOD'] == "POST") {
    $nama = $_POST['nama'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_BCRYPT);

    $check_sql = "SELECT id FROM data_user WHERE email = ?";
    $check_stmt = $conn->prepare($check_sql);
    $check_stmt->bind_param("s", $email);
    $check_stmt->execute();
    $check_stmt->store_result();

    if ($check_stmt->num_rows > 0) {
        $_SESSION['error'] = "Email sudah terdaftar!";
        header("Location: views/signin-signup.php");
        exit();
    } else {
        $sql = "INSERT INTO data_user (nama, email, password) VALUES (?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sss", $nama, $email, $password);

        if ($stmt->execute()) {
            $_SESSION['success'] = "Akun berhasil dibuat. Silakan login.";
            header("Location: views/signin-signup.php");
            exit();
        } else {
            $_SESSION['error'] = "Terjadi kesalahan. Silakan coba lagi.";
            header("Location: views/signin-signup.php");
            exit();
        }
    }
}
?>
