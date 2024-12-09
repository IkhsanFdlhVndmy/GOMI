<?php if (isset($_SESSION['error'])): ?>
    <p class="error"><?= $_SESSION['error']; unset($_SESSION['error']); ?></p>
<?php endif; ?>

<?php if (isset($_SESSION['success'])): ?>
    <p class="success"><?= $_SESSION['success']; unset($_SESSION['success']); ?></p>
<?php endif; ?>

<?php
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");
session_start();
include "../config.php";

if ($_SERVER['REQUEST_METHOD'] == "POST") {
    if (isset($_POST['action']) && $_POST['action'] === "login") {
        // Login Logic
        $email = $_POST['email'];
        $password = $_POST['password'];

        if ($email === "admin@gmail.com" && $password === "admin123") {
            $_SESSION['admin'] = true;
            header("Location: admin.php");
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
            header("Location: userprofile.php");
            exit();
        } else {
            $_SESSION['signin_error'] = "Email atau password salah!";
            header("Location: signin-signup.php");
            exit();
        }
    } elseif (isset($_POST['action']) && $_POST['action'] === "register") {
        $nama = $_POST['nama'];
        $email = $_POST['email'];
        $password = password_hash($_POST['password'], PASSWORD_BCRYPT);

        $check_sql = "SELECT id FROM data_user WHERE email = ?";
        $check_stmt = $conn->prepare($check_sql);
        $check_stmt->bind_param("s", $email);
        $check_stmt->execute();
        $check_stmt->store_result();

        if ($check_stmt->num_rows > 0) {
            // Email sudah terdaftar
            $_SESSION['signup_error'] = "Email sudah terdaftar!";
            $_SESSION['sign_up_mode'] = true; // Tetap di mode Sign-up
            header("Location: signin-signup.php");
            exit();
        } else {
            // Buat akun baru
            $sql = "INSERT INTO data_user (nama, email, password) VALUES (?, ?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("sss", $nama, $email, $password);

            if ($stmt->execute()) {
                $_SESSION['success'] = "Akun berhasil dibuat. Silakan login.";
                unset($_SESSION['sign_up_mode']); // Pindah ke mode Sign-in
                header("Location: signin-signup.php");
                exit();
            } else {
                $_SESSION['error'] = "Terjadi kesalahan. Silakan coba lagi.";
                $_SESSION['sign_up_mode'] = true; // Tetap di mode Sign-up
                header("Location: signin-signup.php");
                exit();
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Signin-signup</title>
    <link rel="stylesheet" href="../assets/stylesss.css">
    <script src="https://kit.fontawesome.com/64d58efce2.js" crossorigin="anonymous"></script>
</head>
<body>
      <div class="container <?= isset($_SESSION['sign_up_mode']) && $_SESSION['sign_up_mode'] ? 'sign-up-mode' : '' ?>">
        <div class="forms-container">
            <div class="signin-signup">
                <!-- Sign In Form -->
                <form action="" method="POST" class="sign-in-form">
                    <h2 class="title">Sign in</h2>
                    <div class="input-field">
                        <i class="fas fa-user"></i>
                        <input type="email" name="email" placeholder="Email" required>
                    </div>
                    <div class="input-field">
                        <i class="fas fa-lock"></i>
                        <input type="password" name="password" placeholder="Password" required>
                    </div>
                    <input type="hidden" name="action" value="login">
                    <input type="submit" value="Login" class="btn solid">

                    <?php if (isset($_SESSION['signin_error'])): ?>
                        <p class="error"><?= $_SESSION['signin_error']; unset($_SESSION['signin_error']); ?></p>
                    <?php endif; ?>
                </form>

                <!-- Sign Up Form -->
                <form action="" method="POST" class="sign-up-form">
                      <h2 class="title">Sign up</h2>
                      <div class="input-field">
                          <i class="fas fa-user"></i>
                          <input type="text" name="nama" placeholder="Username" required>
                      </div>
                      <div class="input-field">
                          <i class="fas fa-envelope"></i>
                          <input type="email" name="email" placeholder="Email" required>
                      </div>
                      <div class="input-field">
                          <i class="fas fa-lock"></i>
                          <input type="password" name="password" placeholder="Password" required>
                      </div>
                      <input type="hidden" name="action" value="register">
                      <input type="submit" class="btn" value="Sign up">

                      <?php if (isset($_SESSION['signup_error'])): ?>
                          <p class="error"><?= $_SESSION['signup_error']; unset($_SESSION['signup_error']); ?></p>
                      <?php elseif (isset($_SESSION['signup_success'])): ?>
                          <p class="success"><?= $_SESSION['signup_success']; unset($_SESSION['signup_success']); ?></p>
                      <?php endif; ?>
                  </form>
            </div>
        </div>

        <!-- Panels -->
        <div class="panels-container">
            <div class="panel left-panel">
                <div class="content">
                    <h3>Pengguna Baru?</h3>
                    <p>Silahkan buat akun baru Anda</p>
                    <button class="btn transparent" id="sign-up-btn">Sign up</button>
                </div>
                <img src="../assets/logo/sampah mengelola.png" class="image" alt="">
            </div>
            <div class="panel right-panel">
                <div class="content">
                    <h3>Sudah Punya Akun?</h3>
                    <p>Segera masuk ke akun Anda</p>
                    <button class="btn transparent" id="sign-in-btn">Sign in</button>
                </div>
                <img src="../assets/logo/sampah mengelola.png" class="image" alt="">
            </div>
        </div>
    </div>
    <script>
        const sign_in_btn = document.querySelector("#sign-in-btn");
        const sign_up_btn = document.querySelector("#sign-up-btn");
        const container = document.querySelector(".container");

        sign_up_btn.addEventListener("click", () => {
            container.classList.add("sign-up-mode");
        });

        sign_in_btn.addEventListener("click", () => {
            container.classList.remove("sign-up-mode");
        });
    </script>
    <?php
    if (isset($_SESSION['sign_up_mode'])) {
        unset($_SESSION['sign_up_mode']);
    }
    ?>
</body>
</html>
