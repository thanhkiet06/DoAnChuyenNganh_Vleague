<?php
require '../auth.php';
require_role('admin');
require '../connect.php';

$binhluan = $conn->query("
    SELECT b.ID_BINH_LUAN, b.NOI_DUNG, b.TEN_DANG_NHAP, b.NGAY_TAO, 
           td.ID_TRAN_DAU, d1.TEN_DOI_BONG AS DOI1, d2.TEN_DOI_BONG AS DOI2 
    FROM BINH_LUAN b 
    JOIN TRAN_DAU td ON b.ID_TRAN_DAU = td.ID_TRAN_DAU
    LEFT JOIN DOI_BONG d1 ON td.ID_DOI_1 = d1.ID_DOI_BONG
    LEFT JOIN DOI_BONG d2 ON td.ID_DOI_2 = d2.ID_DOI_BONG
    ORDER BY b.ID_BINH_LUAN DESC
");
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Quản lý bình luận</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container mt-5">
    <h2>🗨️ Danh sách bình luận</h2>
    <table class="table table-bordered mt-3">
        <thead class="table-light">
            <tr>
                <th>Trận đấu</th>
                <th>Người dùng</th>
                <th>Nội dung</th>
                <th>Thời gian</th>
                <th>Hành động</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($bl = $binhluan->fetch_assoc()) : ?>
                <tr>
                    <td><?= $bl['DOI1'] ?> vs <?= $bl['DOI2'] ?></td>
                    <td><?= htmlspecialchars($bl['TEN_DANG_NHAP']) ?></td>
                    <td><?= nl2br(htmlspecialchars($bl['NOI_DUNG'])) ?></td>
                    <td><?= $bl['NGAY_TAO'] ?></td>
                    <td>
                        <a href="xoa_binhluan.php?id=<?= $bl['ID_BINH_LUAN'] ?>" 
                           onclick="return confirm('Bạn có chắc muốn xoá bình luận này không?')" 
                           class="btn btn-sm btn-danger">Xoá</a>
                    </td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>

    <a href="index.php" class="btn btn-secondary">← Quay lại admin</a>
</div>
</body>
</html>
