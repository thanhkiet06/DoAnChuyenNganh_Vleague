<?php
require '../auth.php';
require_role('viewer');
require '../connect.php';

$result = $conn->query("
    SELECT t.*, g.TEN_GIAI_DAU,
           d1.TEN_DOI_BONG AS DOI1,
           d2.TEN_DOI_BONG AS DOI2
    FROM TRAN_DAU t 
     JOIN GIAI_DAU g ON t.ID_GIAI_DAU = g.ID_GIAI_DAU 
     JOIN DOI_BONG d1 ON t.ID_DOI_1 = d1.ID_DOI_BONG
     JOIN DOI_BONG d2 ON t.ID_DOI_2 = d2.ID_DOI_BONG
    ORDER BY t.NGAY_THI_DAU DESC
");
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Lịch và kết quả thi đấu - V.League</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=Inter:wght@400;600&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background-color: #f9fafc;
        }

        .container {
            padding: 40px 20px;
            max-width: 960px;
        }

        .title {
            font-family: 'Bebas Neue', cursive;
            font-size: 44px;
            color: #d90429;
            text-align: center;
            margin-bottom: 30px;
        }

        .table thead {
            background-color: #d90429;
            color: white;
        }

        .table tbody tr:hover {
            background-color: #f0f0f0;
        }

        .btn-view {
            background-color: #d90429;
            color: white;
            padding: 5px 15px;
            font-size: 14px;
        }

        .btn-view:hover {
            background-color: #b40221;
        }

        .btn-comment, .btn-vote {
            font-size: 13px;
            padding: 4px 10px;
        }

        .btn-back {
            margin-top: 30px;
            background-color: #6c757d;
            color: white;
        }
    </style>
</head>
<body>

<div class="container">
    <h1 class="title">📅 Lịch & Kết Quả Thi Đấu</h1>

    <div class="table-responsive">
        <table class="table table-bordered text-center align-middle">
            <thead>
                <tr>
                    <th>Ngày</th>
                    <th>Địa điểm</th>
                    <th>Giải đấu</th>
                    <th>Trận đấu</th>
                    <th>Kết quả</th>
                    <th>Chi tiết</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $result->fetch_assoc()) { ?>
                <tr>
                    <td><?= htmlspecialchars($row['NGAY_THI_DAU']) ?></td>
                    <td><?= htmlspecialchars($row['DIA_DIEM']) ?></td>
                    <td><?= htmlspecialchars($row['TEN_GIAI_DAU']) ?></td>
                    <td><strong><?= htmlspecialchars($row['DOI1']) ?> vs <?= htmlspecialchars($row['DOI2']) ?></strong></td>
                    <td><?= htmlspecialchars($row['KET_QUA']) ?></td>
                    <td>
                        <a href="chitiet_tran.php?id=<?= $row['ID_TRAN_DAU'] ?>" class="btn btn-view mb-1">Xem</a>
                        <a href="binhluan_tran.php?id=<?= $row['ID_TRAN_DAU'] ?>" class="btn btn-outline-primary btn-sm btn-comment">💬 Bình luận</a>
                        <a href="binhchon.php?id=<?= $row['ID_TRAN_DAU'] ?>" class="btn btn-outline-success btn-sm btn-vote">⭐ Bình chọn</a>
                    </td>
                </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>

    <div class="text-center">
        <a href="index.php" class="btn btn-back px-4">← Về trang chính</a>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
