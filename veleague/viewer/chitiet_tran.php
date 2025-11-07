<?php
require '../connect.php';
$id = $_GET['id'];

// Thông tin trận đấu
$tran = $conn->query("SELECT * FROM TRAN_DAU WHERE ID_TRAN_DAU = $id")->fetch_assoc();

// Thông tin đội bóng
$doi1 = $conn->query("SELECT TEN_DOI_BONG FROM DOI_BONG WHERE ID_DOI_BONG = {$tran['ID_DOI_1']}")->fetch_assoc();
$doi2 = $conn->query("SELECT TEN_DOI_BONG FROM DOI_BONG WHERE ID_DOI_BONG = {$tran['ID_DOI_2']}")->fetch_assoc();

// Danh sách sự kiện
$sukien = $conn->query("
    SELECT sk.*, c.HO_TEN, d.TEN_DOI_BONG 
    FROM SU_KIEN_TRAN_DAU sk 
    JOIN CAU_THU c ON sk.ID_CAU_THU = c.ID_CAU_THU 
    JOIN DOI_BONG d ON c.ID_DOI_BONG = d.ID_DOI_BONG 
    WHERE sk.ID_TRAN_DAU = $id 
    ORDER BY sk.THOI_GIAN ASC
");
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Chi tiết trận đấu - V.League</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Bootstrap + Font -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=Inter:wght@400;600&display=swap" rel="stylesheet">

    <style>
        body {
            font-family: 'Inter', sans-serif;
            background-color: #f4f6fa;
        }

        .container {
            padding: 40px 20px;
            max-width: 800px;
        }

        .title {
            font-family: 'Bebas Neue', cursive;
            font-size: 44px;
            color: #d90429;
        }

        .match-info li {
            margin-bottom: 8px;
        }

        .event-table thead {
            background-color: #d90429;
            color: white;
        }

        .event-table tbody tr:hover {
            background-color: #f1f1f1;
        }

        .btn-back {
            background-color: #d90429;
            color: white;
            font-weight: 500;
        }

        .btn-back:hover {
            background-color: #b40221;
        }
    </style>
</head>
<body>

<div class="container">
    <h1 class="text-center title">Chi tiết trận đấu</h1>

    <ul class="list-unstyled match-info mt-4">
        <li><strong>Ngày:</strong> <?= $tran['NGAY_THI_DAU'] ?></li>
        <li><strong>Địa điểm:</strong> <?= $tran['DIA_DIEM'] ?></li>
        <li><strong>Đội:</strong> <?= $doi1['TEN_DOI_BONG'] ?> <strong>vs</strong> <?= $doi2['TEN_DOI_BONG'] ?></li>
        <li><strong>Kết quả:</strong> <?= $tran['KET_QUA'] ?></li>
    </ul>

    <h4 class="mt-4 mb-3">🎯 Sự kiện trong trận</h4>
    <?php if ($sukien->num_rows > 0) { ?>
    <div class="table-responsive">
        <table class="table table-bordered text-center event-table">
            <thead>
                <tr>
                    <th>Phút</th><th>Cầu thủ</th><th>Đội bóng</th><th>Sự kiện</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($s = $sukien->fetch_assoc()) { ?>
                <tr>
                    <td><?= $s['THOI_GIAN'] ?>'</td>
                    <td class="text-start"><?= $s['HO_TEN'] ?></td>
                    <td><?= $s['TEN_DOI_BONG'] ?></td>
                    <td><?= $s['LOAI_SU_KIEN'] ?></td>
                </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
    <?php } else {
        echo "<p class='text-muted'>Không có sự kiện nào trong trận này.</p>";
    } ?>

    <div class="mt-4">
        <a href="trandau.php" class="btn btn-back">← Quay lại lịch & kết quả</a>
    </div>
</div>

<!-- Bootstrap Bundle -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
