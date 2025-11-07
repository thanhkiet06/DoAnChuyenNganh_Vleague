<?php
require '../auth.php';
require_role('admin');
require '../connect.php';

// Thống kê bình chọn
$thongke = $conn->query("
    SELECT t.ID_TRAN_DAU, t.NGAY_THI_DAU, d1.TEN_DOI_BONG AS DOI1, d2.TEN_DOI_BONG AS DOI2,
           c.HO_TEN, COUNT(b.ID_BINH_CHON) AS SO_LUOT
    FROM BINH_CHON_CAU_THU b
    JOIN CAU_THU c ON b.ID_CAU_THU = c.ID_CAU_THU
    JOIN TRAN_DAU t ON b.ID_TRAN_DAU = t.ID_TRAN_DAU
    JOIN DOI_BONG d1 ON t.ID_DOI_1 = d1.ID_DOI_BONG
    JOIN DOI_BONG d2 ON t.ID_DOI_2 = d2.ID_DOI_BONG
    GROUP BY b.ID_TRAN_DAU, b.ID_CAU_THU
    ORDER BY t.NGAY_THI_DAU DESC, SO_LUOT DESC
");
?>

<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <title>Thống kê bình chọn cầu thủ - Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
    body {
        font-family: 'Inter', sans-serif;
        background-color: #f8f9fa;
        padding: 30px;
    }

    .title {
        font-size: 32px;
        color: #d90429;
        margin-bottom: 20px;
        font-weight: bold;
    }
    </style>
</head>

<body>

    <div class="container">
        <h1 class="title">📊 Thống kê bình chọn cầu thủ xuất sắc</h1>

        <?php if (isset($_GET['reset']) && $_GET['reset'] == 'ok'): ?>
        <div class="alert alert-success">✅ Đã reset bình chọn thành công!</div>
        <?php endif; ?>

        <div class="table-responsive">
            <table class="table table-bordered table-hover text-center">
                <thead class="table-danger">
                    <tr>
                        <th>Ngày thi đấu</th>
                        <th>Trận đấu</th>
                        <th>Cầu thủ</th>
                        <th>Số lượt bình chọn</th>
                        <th>Hành động</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($thongke->num_rows > 0): ?>
                    <?php while ($row = $thongke->fetch_assoc()): ?>
                    <tr>
                        <td><?= htmlspecialchars($row['NGAY_THI_DAU']) ?></td>
                        <td><?= htmlspecialchars($row['DOI1']) ?> vs <?= htmlspecialchars($row['DOI2']) ?></td>
                        <td><?= htmlspecialchars($row['HO_TEN']) ?></td>
                        <td><strong><?= $row['SO_LUOT'] ?></strong></td>
                        <td>
                            <a href="reset_binhchon.php?id_tran=<?= $row['ID_TRAN_DAU'] ?>"
                                class="btn btn-danger btn-sm"
                                onclick="return confirm('Bạn có chắc chắn muốn reset bình chọn của trận này?')">
                                Reset
                            </a>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                    <?php else: ?>
                    <tr>
                        <td colspan="5" class="text-muted">Chưa có dữ liệu bình chọn!</td>
                    </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <a href="index.php" class="btn btn-secondary mt-3">← Quay lại </a>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>