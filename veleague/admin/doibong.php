<?php
require '../auth.php';
require_role('admin');
require '../connect.php';

$result = $conn->query("SELECT d.*, g.TEN_GIAI_DAU FROM DOI_BONG d 
                        LEFT JOIN GIAI_DAU g ON d.ID_GIAI_DAU = g.ID_GIAI_DAU");
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Quản lý đội bóng</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=Inter:wght@400;600&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background-color: #f8f9fa;
            padding: 40px;
        }

        .heading {
            font-family: 'Bebas Neue', sans-serif;
            font-size: 36px;
            color: #2c3e50;
            margin-bottom: 20px;
        }

        .table th {
            background-color: #e9ecef;
        }

        .btn-sm {
            font-size: 14px;
        }

        .badge {
            font-size: 13px;
        }

        img.logo {
            height: 50px;
            border-radius: 5px;
        }
    </style>
</head>
<body>

<div class="container">
    <h1 class="heading"><i class="bi bi-shield-fill me-2"></i>Quản lý đội bóng</h1>

    <div class="mb-3">
        <a href="them_doibong.php" class="btn btn-primary btn-sm"><i class="bi bi-plus-circle me-1"></i>Thêm đội bóng</a>
        <a href="index.php" class="btn btn-outline-secondary btn-sm float-end"><i class="bi bi-arrow-left"></i> Về trang admin</a>
    </div>

    <table class="table table-bordered table-hover align-middle shadow-sm bg-white">
        <thead class="table-light">
            <tr>
                <th>ID</th>
                <th>Logo</th>
                <th>Tên đội</th>
                <th>Huấn luyện viên</th>
                <th>Giải đấu</th>
                <th style="width: 140px;">Hành động</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = $result->fetch_assoc()) { ?>
            <tr>
                <td><?= $row['ID_DOI_BONG'] ?></td>
                <td>
                    <?php if (!empty($row['LOGO']) && file_exists("../" . $row['LOGO'])): ?>
                        <img src="../<?= htmlspecialchars($row['LOGO']) ?>" alt="Logo" class="logo">
                    <?php else: ?>
                        <span class="text-muted">Không có</span>
                    <?php endif; ?>
                </td>
                <td><strong><?= $row['TEN_DOI_BONG'] ?></strong></td>
                <td><?= $row['HUAN_LUYEN_VIEN'] ?></td>
                <td><span class="badge bg-info text-dark"><?= $row['TEN_GIAI_DAU'] ?></span></td>
                <td>
                    <a href="sua_doibong.php?id=<?= $row['ID_DOI_BONG'] ?>" class="btn btn-warning btn-sm"><i class="bi bi-pencil-square"></i> Sửa</a>
                    <a href="xoa_doibong.php?id=<?= $row['ID_DOI_BONG'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Xoá đội này?')"><i class="bi bi-trash"></i> Xoá</a>
                </td>
            </tr>
            <?php } ?>
        </tbody>
    </table>
</div>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
