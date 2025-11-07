<?php
require '../auth.php';
require_role('hlv');
require '../connect.php';

$hlv = $_SESSION['ten_dang_nhap'];
$team = $conn->query("SELECT * FROM DOI_BONG WHERE HUAN_LUYEN_VIEN = '$hlv'")->fetch_assoc();

if (!$team) {
    echo "<div class='container mt-5'><p>Bạn chưa được gán vào đội nào.</p><a href='index.php' class='btn btn-secondary mt-2'>Quay lại</a></div>";
    exit;
}

// Lấy tên giải đấu
$id_giai = $team['ID_GIAI_DAU'];
$giai = $conn->query("SELECT TEN_GIAI_DAU FROM GIAI_DAU WHERE ID_GIAI_DAU = $id_giai")->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Thông tin đội bóng - HLV</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    
    <!-- Bootstrap & Fonts -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=Inter:wght@400;600&display=swap" rel="stylesheet">
    
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background-color: #f8f9fa;
        }

        .container {
            padding: 50px 20px;
            max-width: 700px;
        }

        h2 {
            font-family: 'Bebas Neue', cursive;
            font-size: 38px;
            color: #d90429;
            margin-bottom: 30px;
        }

        .info-box {
            background-color: #ffffff;
            border-radius: 12px;
            box-shadow: 0 6px 14px rgba(0,0,0,0.08);
            padding: 30px;
        }

        .info-box li {
            font-size: 18px;
            margin-bottom: 10px;
        }

        .btn-back {
            margin-top: 30px;
        }
    </style>
</head>
<body>

<div class="container">
    <h2>📘 Thông tin đội bóng của bạn</h2>

    <div class="info-box">
        <ul>
            <li><strong>Tên đội:</strong> <?= $team['TEN_DOI_BONG'] ?></li>
            <li><strong>Giải đấu:</strong> <?= $giai['TEN_GIAI_DAU'] ?? 'Chưa rõ' ?></li>
            <li><strong>Huấn luyện viên:</strong> <?= $team['HUAN_LUYEN_VIEN'] ?></li>
        </ul>
    </div>

    <a href="index.php" class="btn btn-secondary btn-back">← Quay lại trang chính</a>
</div>

<!-- Bootstrap -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
