
<?php
include "../config.php";
include "../session.php";

// Pastikan session dimulai

// Ambil user_id dari session
if (!isset($_SESSION['user_id'])) {
    header("Location: signin-signup.php");
    exit();
}

// Ambil data user berdasarkan sesi aktif
$user_id = $_SESSION['user_id'];
$sql = "SELECT * FROM data_user WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();

if ($_SERVER['REQUEST_METHOD'] == "POST") {
    // Ambil data JSON dari AJAX
    $data = json_decode(file_get_contents("php://input"), true);

    if ($data) {
        $type = $data['type']; // 'uang' atau 'hadiah'
        $value = intval($data['value']);
        $points_needed = intval($data['points_needed']);

        if ($user['point'] >= $points_needed) {
            $new_points = $user['point'] - $points_needed;

            // Update poin di database
            $update_sql = "UPDATE data_user SET point = ? WHERE id = ?";
            $update_stmt = $conn->prepare($update_sql);
            $update_stmt->bind_param("ii", $new_points, $user_id);
            $update_stmt->execute();

            // Insert ke log transaksi
            $log_sql = "INSERT INTO transaksi (user_id, type, value, points_used) VALUES (?, ?, ?, ?)";
            $log_stmt = $conn->prepare($log_sql);
            $log_stmt->bind_param("isii", $user_id, $type, $value, $points_needed);
            $log_stmt->execute();

            // Kirim response JSON sukses
            echo json_encode([
                "success" => true,
                "message" => "Berhasil menukarkan poin!",
                "new_points" => $new_points
            ]);
        } else {
            // Kirim response JSON gagal
            echo json_encode([
                "success" => false,
                "message" => "Poin tidak mencukupi untuk pertukaran ini."
            ]);
        }
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Dashboard - Gomi</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
    <style>
        body {
            background-color: #f8f9fa;
            font-family: Arial, sans-serif;
        }
        .dashboard-header {
            background-color: #ffffff;
            padding: 20px;
            border-bottom: 1px solid #ddd;
        }
        .card {
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            margin-bottom: 20px;
        }
        .button {
            background-color: #5BB658;
            color: #fff;
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
        }
        .button:hover {
            background-color: #00741D;
        }
        .dropdown-menu {
        border-radius: 5px;
        box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.1);
        }
        .dropdown-item:hover {
            background-color: #f8f9fa;
            color: #000;
        }
        .button2 {
        display: inline-block;
        padding: 10px 20px;
        font-size: 16px;
        text-decoration: none; /* Hilangkan garis bawah */
        color: #fff; /* Warna teks */
        background-color: #5BB658; /* Warna latar belakang tombol */
        border: none;
        border-radius: 5px;
        cursor: pointer;
        text-align: center;
        transition: background-color 0.3s;
    }

    .button2:hover {
        background-color: #00741D; /* Warna saat hover */
    }
    </style>
</head>
<body>
<div class="container-fluid">
    <div class="row">
        
        <!-- Content -->
        <div class="col-md-12">
          <div class="dashboard-header d-flex justify-content-between align-items-center">
              <h1>Hello, <?= htmlspecialchars($user['nama']) ?>!</h1>
              <div class="dropdown">
                  <img 
                      src="https://via.placeholder.com/50" 
                      alt="User Avatar" 
                      class="rounded-circle dropdown-toggle" 
                      id="avatarDropdown" 
                      data-bs-toggle="dropdown" 
                      aria-expanded="false"
                      style="cursor: pointer;">
                  <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="avatarDropdown">
                      <li><a class="dropdown-item" href="../logout.php">Logout</a></li>
                  </ul>
              </div>
          </div>
            <div class="container mt-4">
                <div class="row">
                    <div class="col-md-4">
                        <div class="card p-4 ">
                            <h4>Point Saya:</h4>
                            <h2 id="point-display"><?= number_format($user['point'], 0) ?></h2>
                            <a href="userprofile.php" class="button2">Kembali Ke User Profile</a>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-4">
                        <div class=" p-2 ">
                            <h4>Tukar Point Menjadi Uang:</h4>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-4">
                        <div class="card p-5">
                            <h4>Tukarkan Menjadi Uang </h4>
                            <img src="../assets/gambarhadiah/50rb.jpeg" class="mt-2" alt="50rb">
                            <form onsubmit="return tukarPoin(event, 'uang', 50000, 100);">
                                <button class="button mt-4">Tukarkan Rp50.000 (100 Poin)</button>
                            </form>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card p-5">
                            <h4>Tukarkan Menjadi Uang </h4>
                            <img src="../assets/gambarhadiah/100rb.jpg" class="mt-2" alt="100rb">
                            <form onsubmit="return tukarPoin(event, 'uang', 100000, 180);">
                                <button class="button mt-4">Tukarkan Rp100.000 (180 Poin)</button>
                            </form>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card p-5">
                            <h4>Tukarkan Menjadi Uang</h4>
                            <img src="../assets/gambarhadiah/150rb.png" width="180" class="mt-2" alt="150rb">
                            <form onsubmit="return tukarPoin(event, 'uang', 150000, 250);">
                                <button class="button mt-3">Tukarkan Rp150.000 (250 Poin)</button>
                            </form>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-4">
                        <div class=" p-2 ">
                            <h4>Tukar Point Menjadi Hadiah:</h4>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-4">
                        <div class="card p-4">
                            <h4 class="mt-4">Tukarkan Menjadi Voucher Game:</h4>
                            <img src="../assets/gambarhadiah/game.png" class="mt-5" alt="game">
                            <form onsubmit="return tukarPoin(event, 'hadiah', 50000, 100);">
                                <button class="button mt-5">Voucher Game (100 Poin)</button>
                            </form>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card p-5">
                            <h4>Tukarkan Voucher Belanja</h4>
                            <img src="../assets/gambarhadiah/belanja.png" class="mt-2" alt="belanja">
                            <form onsubmit="return tukarPoin(event, 'hadiah', 100000, 180);">
                                <button class="button mt-5">Voucher Belanja (150 Poin)</button>
                            </form>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card p-5">
                            <h4>Tukarkan Voucher Diskon</h4>
                            <img src="../assets/gambarhadiah/diskon.png" class="mt-2" alt="diskon">
                            <form onsubmit="return tukarPoin(event, 'hadiah', 150000, 250);">
                                <button class="button mt-5">Voucher Diskon (200 Poin)</button>
                            </form>
                        </div>
                    </div>
                </div>

        
            </div>
        </div>
    </div>
</div>

<script>
        function tukarPoin(event, type, value, pointsNeeded) {
    event.preventDefault();
    if (confirm("Yakin Menukar Point?")) {
        fetch("userpointtest.php", {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify({ type, value, points_needed: pointsNeeded }),
        })
        .then((response) => response.json())
        .then((data) => {
            alert(data.message);
            if (data.success) {
                // Perbarui tampilan poin
                document.getElementById("point-display").textContent = data.new_points.toLocaleString();
            }
        })
        .catch((error) => console.error("Error:", error));
    }
}

    </script>
</body>
</html>
