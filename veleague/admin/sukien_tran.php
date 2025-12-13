<?php
require '../auth.php';
require_role('admin');
require '../connect.php';

$trands = $conn->query("
    SELECT t.*, d1.TEN_DOI_BONG AS DOI1, d2.TEN_DOI_BONG AS DOI2 
    FROM TRAN_DAU t 
    JOIN DOI_BONG d1 ON t.ID_DOI_1 = d1.ID_DOI_BONG 
    JOIN DOI_BONG d2 ON t.ID_DOI_2 = d2.ID_DOI_BONG 
    ORDER BY t.NGAY_THI_DAU DESC
");
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Sự kiện trận đấu</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&family=Bebas+Neue&display=swap" rel="stylesheet">
    <style>
      body {
            font-family: 'Inter', sans-serif;
            background-color: #eef2f6;
            padding: 25px;
            color: #2c3e50;
        }

        .heading {
            font-size: 40px;
            font-weight: 600;
            color: #1b263b;
            margin-bottom: 25px;
            letter-spacing: 0.5px;
        }
        .btn-sm {
            font-size: 14px;
        }
    </style>
</head>
<body>

<div class="container">
    <h1 class="heading mb-4"><i class="bi bi-flag-fill me-2"></i>Chọn trận để quản lý sự kiện</h1>

    <table class="table table-bordered table-hover align-middle shadow-sm bg-white">
        <thead class="table-light">
            <tr>
                <th>Ngày</th>
                <th>Đội 1</th>
                <th>Đội 2</th>
                <th>Kết quả</th>
                <th style="width: 150px;">Hành động</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = $trands->fetch_assoc()) { ?>
            <tr>
                <td><i class="bi bi-calendar-event me-1 text-primary"></i><?= date("d/m/Y", strtotime($row['NGAY_THI_DAU'])) ?></td>
                <td><strong><?= $row['DOI1'] ?></strong></td>
                <td><strong><?= $row['DOI2'] ?></strong></td>
                <td><?= $row['KET_QUA'] ?></td>
                <td>
                    <a href="sukien_tran_detail.php?id_tran=<?= $row['ID_TRAN_DAU'] ?>" class="btn btn-outline-danger btn-sm">
                        <i class="bi bi-eye-fill me-1"></i> Xem sự kiện
                    </a>
                </td>
            </tr>
            <?php } ?>
        </tbody>
    </table>

    <a href="index.php" class="btn btn-secondary btn-sm mt-3">
        <i class="bi bi-arrow-left"></i> Về trang admin
    </a>
</div>

<!-- Bootstrap Bundle -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
