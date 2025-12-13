<?php
require '../connect.php';
$result = $conn->query("SELECT bxh.*, d.TEN_DOI_BONG 
                        FROM BANG_XEP_HANG bxh
                        JOIN DOI_BONG d ON bxh.ID_DOI_BONG = d.ID_DOI_BONG
                        ORDER BY DIEM_SO DESC, HIEU_SO DESC");
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Bảng xếp hạng - V.League 2025</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Bootstrap + Icon + Fonts -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=Inter:wght@400;600&display=swap" rel="stylesheet">

   <style>
    /* Import font Google */
    @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&family=Roboto:wght@300;400;500;700&display=swap');

    body {
        font-family: 'Roboto', sans-serif;
        background-color: #f2f4f8;
        font-size: 16px;
        line-height: 1.6;
    }

    .title {
        font-family: 'Poppins', sans-serif;
        font-size: 46px;
        color: #d90429;
        margin-top: 30px;
        font-weight: 600;
        letter-spacing: 1px;
    }

    .table thead {
        background-color: #d90429;
        color: white;
        font-weight: 600;
        font-size: 17px;
    }

    .table tbody tr {
        font-size: 16px;
    }

    .table tbody tr:hover {
        background-color: #f8f9fa;
    }

    .container {
        padding-top: 40px;
        padding-bottom: 60px;
    }

    .btn-back {
        background-color: #d90429;
        color: white;
        font-weight: 600;
        padding: 8px 18px;
        border-radius: 6px;
        font-family: 'Poppins', sans-serif;
    }

    .btn-back:hover {
        background-color: #b40221;
    }
</style>

</head>
<body>

<div class="container">
    <h1 class="text-center title">🏆 BẢNG XẾP HẠNG V.LEAGUE 2025</h1>
    <p class="text-center text-muted mb-4">Cập nhật mới nhất sau mỗi vòng đấu</p>

    <div class="table-responsive">
        <table class="table table-bordered table-striped text-center align-middle">
            <thead>
                <tr>
                    <th>Hạng</th>
                    <th>Đội bóng</th>
                    <th>Điểm</th>
                    <th>Trận</th>
                    <th>Thắng</th>
                    <th>Hòa</th>
                    <th>Thua</th>
                    <th>Bàn thắng</th>
                    <th>Bàn thua</th>
                    <th>Hiệu số</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                $h = 1;
                while ($row = $result->fetch_assoc()) { ?>
                <tr>
                    <td><?= $h++ ?></td>
                    <td class="text-start ps-3"><?= $row['TEN_DOI_BONG'] ?></td>
                    <td><?= $row['DIEM_SO'] ?></td>
                    <td><?= $row['SO_TRAN'] ?></td>
                    <td><?= $row['SO_THANG'] ?></td>
                    <td><?= $row['SO_HOA'] ?></td>
                    <td><?= $row['SO_THA'] ?></td>
                    <td><?= $row['BAN_THANG'] ?></td>
                    <td><?= $row['BAN_THA'] ?></td>
                    <td><?= $row['HIEU_SO'] ?></td>
                </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>

    <div class="text-center mt-4">
        <a href="index.php" class="btn btn-back px-4">← Về trang chính</a>
    </div>
</div>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
