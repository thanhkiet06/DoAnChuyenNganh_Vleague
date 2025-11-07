<?php
require '../auth.php';
require_role('admin');
require '../connect.php';

$id_tran = $_GET['id_tran'];
$tran = $conn->query("SELECT * FROM TRAN_DAU WHERE ID_TRAN_DAU = $id_tran")->fetch_assoc();
$sukien = $conn->query("
    SELECT sk.*, c.HO_TEN 
    FROM SU_KIEN_TRAN_DAU sk 
    JOIN CAU_THU c ON sk.ID_CAU_THU = c.ID_CAU_THU 
    WHERE sk.ID_TRAN_DAU = $id_tran 
    ORDER BY sk.THOI_GIAN ASC
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
            background-color: #f8f9fa;
            padding: 40px;
        }

        .heading {
            font-family: 'Bebas Neue', sans-serif;
            font-size: 32px;
            color: #2c3e50;
        }

        .btn-sm {
            font-size: 14px;
        }
    </style>
</head>
<body>

<div class="container">
    <h1 class="heading mb-3"><i class="bi bi-flag-fill me-2"></i>Danh sách sự kiện trận đấu</h1>
    <p class="mb-4">
        <strong>Trận:</strong> <?= $tran['KET_QUA'] ?> |
        <strong>Ngày:</strong> <?= date("d/m/Y", strtotime($tran['NGAY_THI_DAU'])) ?>
    </p>

    <div class="mb-3">
        <a href="them_sukien.php?id_tran=<?= $id_tran ?>" class="btn btn-primary btn-sm">
            <i class="bi bi-plus-circle me-1"></i> Thêm sự kiện
        </a>
        <a href="sukien_tran.php" class="btn btn-outline-secondary btn-sm float-end">
            <i class="bi bi-arrow-left"></i> Quay lại danh sách trận
        </a>
    </div>

    <table class="table table-bordered table-hover bg-white shadow-sm align-middle">
        <thead class="table-light">
            <tr>
                <th style="width: 120px;">Thời gian</th>
                <th>Cầu thủ</th>
                <th>Loại sự kiện</th>
                <th style="width: 100px;">Hành động</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($s = $sukien->fetch_assoc()) { ?>
            <tr>
                <td><i class="bi bi-clock me-1 text-info"></i><?= $s['THOI_GIAN'] ?>'</td>
                <td><?= $s['HO_TEN'] ?></td>
                <td><?= $s['LOAI_SU_KIEN'] ?></td>
                <td>
                    <a href="xoa_sukien.php?id=<?= $s['ID_SU_KIEN'] ?>&id_tran=<?= $id_tran ?>" class="btn btn-danger btn-sm" onclick="return confirm('Xoá sự kiện này?')">
                        <i class="bi bi-trash-fill"></i> Xoá
                    </a>
                </td>
            </tr>
            <?php } ?>
            <?php if ($sukien->num_rows === 0) { ?>
            <tr>
                <td colspan="4" class="text-center text-muted">Chưa có sự kiện nào.</td>
            </tr>
            <?php } ?>
        </tbody>
    </table>
</div>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
