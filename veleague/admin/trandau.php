<?php
require '../auth.php';
require_role('admin');
require '../connect.php';

$result = $conn->query("SELECT t.*, g.TEN_GIAI_DAU 
                        FROM TRAN_DAU t 
                        LEFT JOIN GIAI_DAU g ON t.ID_GIAI_DAU = g.ID_GIAI_DAU");
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Quản lý trận đấu</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=Inter:wght@400;600&display=swap" rel="stylesheet">
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

        .table th {
            background-color: #e9ecef;
        }

        .btn-sm {
            font-size: 14px;
        }
    </style>
</head>
<body>

<div class="container">
    <h1 class="heading"><i class="bi bi-calendar-event-fill me-2"></i>Quản lý trận đấu</h1>

    <div class="mb-3">
        <a href="them_trandau.php" class="btn btn-primary btn-sm"><i class="bi bi-plus-circle me-1"></i>Thêm trận đấu</a>
        <a href="index.php" class="btn btn-outline-secondary btn-sm float-end"><i class="bi bi-arrow-left"></i> Về trang admin</a>
    </div>

    <table class="table table-bordered table-hover align-middle shadow-sm bg-white">
        <thead class="table-light">
            <tr>
                <th>Ngày</th>
                <th>Địa điểm</th>
                <th>Giải đấu</th>
                <th>Kết quả</th>
                <th style="width: 140px;">Hành động</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = $result->fetch_assoc()) { ?>
            <tr>
                <td><?= date("d/m/Y", strtotime($row['NGAY_THI_DAU'])) ?></td>
                <td><?= $row['DIA_DIEM'] ?></td>
                <td><span class="badge bg-info text-dark"><?= $row['TEN_GIAI_DAU'] ?></span></td>
                <td><strong><?= $row['KET_QUA'] ?></strong></td>
                <td>
                    <a href="sua_trandau.php?id=<?= $row['ID_TRAN_DAU'] ?>" class="btn btn-warning btn-sm">
                        <i class="bi bi-pencil-square"></i> Sửa
                    </a>
                    <a href="xoa_trandau.php?id=<?= $row['ID_TRAN_DAU'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Xoá trận này?')">
                        <i class="bi bi-trash"></i> Xoá
                    </a>
                </td>
            </tr>
            <?php } ?>
        </tbody>
    </table>
</div>

<!-- Bootstrap Bundle -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
