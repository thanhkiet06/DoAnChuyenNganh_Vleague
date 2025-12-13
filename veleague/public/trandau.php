<?php
require '../connect.php';
$result = $conn->query("SELECT t.*, g.TEN_GIAI_DAU,
                        d1.TEN_DOI_BONG AS DOI1,
                        d2.TEN_DOI_BONG AS DOI2
                        FROM TRAN_DAU t 
                         JOIN GIAI_DAU g ON t.ID_GIAI_DAU = g.ID_GIAI_DAU 
                         JOIN DOI_BONG d1 ON t.ID_DOI_1 = d1.ID_DOI_BONG
                         JOIN DOI_BONG d2 ON t.ID_DOI_2 = d2.ID_DOI_BONG
                        ORDER BY t.NGAY_THI_DAU DESC");
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Lịch và kết quả thi đấu - V.League</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Bootstrap & Fonts -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&family=Rubik:wght@400;500;600&display=swap" rel="stylesheet">

    <style>
        body {
            font-family: 'Inter', sans-serif;
            background-color: #f4f6f9;
            padding-bottom: 40px;
        }

        .container {
            padding: 40px 20px;
            max-width: 960px;
        }

        .title {
            font-family: 'Rubik', sans-serif;
            font-size: 36px;
            font-weight: 600;
            color: #e63946;
            text-align: center;
            margin-bottom: 25px;
        }

        /* Bảng */
        .table {
            border-radius: 12px;
            overflow: hidden;
            font-size: 15px;
        }

        .table thead {
            background-color: #e63946;
            color: white;
            font-size: 16px;
        }

        .table tbody tr:nth-child(even) {
            background-color: #fafafa;
        }

        .table tbody tr:hover {
            background-color: #f1f1f1;
        }

        .btn-view {
            background-color: #e63946;
            color: white;
            padding: 6px 15px;
            border-radius: 6px;
            font-size: 14px;
            font-weight: 500;
        }

        .btn-view:hover {
            background-color: #b72c37;
        }

        .btn-back {
            margin-top: 30px;
            background-color: #6c757d;
            color: white;
            padding: 8px 22px;
            border-radius: 8px;
            font-size: 15px;
        }

        .btn-back:hover {
            background-color: #5a6268;
        }

        /* Cell chữ đậm */
        td strong {
            font-family: 'Rubik', sans-serif;
            font-weight: 600;
        }
    </style>
</head>
<body>

<div class="container">
    <h1 class="title">📅 Lịch & Kết Quả Thi Đấu</h1>

    <div class="table-responsive shadow-sm">
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
                    <td><?= $row['NGAY_THI_DAU'] ?></td>
                    <td><?= $row['DIA_DIEM'] ?></td>
                    <td><?= $row['TEN_GIAI_DAU'] ?></td>
                    <td><strong><?= $row['DOI1'] ?> vs <?= $row['DOI2'] ?></strong></td>
                    <td><?= $row['KET_QUA'] ?></td>
                    <td>
                        <a href="chitiet_tran.php?id=<?= $row['ID_TRAN_DAU'] ?>" class="btn btn-view">Xem</a>
                    </td>
                </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>

    <div class="text-center">
        <a href="index.php" class="btn btn-back">← Về trang chính</a>
    </div>
</div>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
