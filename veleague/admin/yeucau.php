<?php
require '../auth.php';
require_role('admin');
require '../connect.php';

$result = $conn->query("SELECT y.*, n.TEN_DANG_NHAP FROM YEU_CAU_USER y 
                        JOIN NGUOI_DUNG n ON y.ID_NGUOI_DUNG = n.ID_NGUOI_DUNG");
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Yêu cầu người dùng</title>
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

        .badge {
            font-size: 13px;
        }
    </style>
</head>
<body>

<div class="container">
    <h1 class="heading mb-4"><i class="bi bi-envelope-open-fill me-2"></i>Danh sách yêu cầu từ người dùng</h1>

    <table class="table table-bordered table-hover align-middle bg-white shadow-sm">
        <thead class="table-light">
            <tr>
                <th>ID</th>
                <th>Người gửi</th>
                <th>Loại</th>
                <th>Nội dung</th>
                <th>Ngày</th>
                <th>Trạng thái</th>
                <th>Hành động</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = $result->fetch_assoc()) { ?>
            <tr>
                <td><?= $row['ID_YEU_CAU'] ?></td>
                <td><i class="bi bi-person-circle me-1"></i><?= $row['TEN_DANG_NHAP'] ?></td>
                <td><?= $row['LOAI_YEU_CAU'] ?></td>
                <td><?= $row['NOI_DUNG'] ?></td>
                <td><?= date("d/m/Y", strtotime($row['NGAY_TAO'])) ?></td>
                <td>
                    <?php
                        $status = $row['TRANG_THAI'];
                        $badge = match ($status) {
                            'Chờ duyệt' => 'warning',
                            'Đã xử lý' => 'success',
                            'Từ chối' => 'danger',
                            default => 'secondary'
                        };
                    ?>
                    <span class="badge bg-<?= $badge ?>"><?= $status ?></span>
                </td>
                <td>
                    <a href="duyet_yeucau.php?id=<?= $row['ID_YEU_CAU'] ?>" class="btn btn-outline-primary btn-sm">
                        <i class="bi bi-check-circle"></i> Duyệt
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

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
