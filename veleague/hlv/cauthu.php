<?php
require '../auth.php';
require_role('hlv');
require '../connect.php';

$hlv = $_SESSION['ten_dang_nhap'];
$team = $conn->query("SELECT ID_DOI_BONG FROM DOI_BONG WHERE HUAN_LUYEN_VIEN = '$hlv'")->fetch_assoc();

if (!$team) {
    echo "<div class='container mt-5'><p>Bạn chưa có đội bóng.</p><a href='index.php' class='btn btn-secondary mt-2'>Quay lại</a></div>";
    exit;
}

$id_doi = $team['ID_DOI_BONG'];

$keyword = "";          
$where = "ID_DOI_BONG = $id_doi";

    if (!empty($_GET['keyword'])) {
         $keyword = $conn->real_escape_string($_GET['keyword']);
         $where .= " AND (HO_TEN LIKE '%$keyword%' 
                OR VI_TRI LIKE '%$keyword%'
                OR SO_AO LIKE '%$keyword%')";
}

$sql = "SELECT * FROM CAU_THU WHERE $where";
$result = $conn->query($sql);
?>



<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Quản lý cầu thủ - HLV</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Bootstrap & Fonts -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=Inter:wght@400;600&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background-color: #f7f9fb;
        }

        .container {
            padding-top: 42px;
            max-width: 1100px;
        }

        h2 {
            font-family: 'Inter', sans-serif;
            font-size: 42px;
            color: #d90429;
            margin-bottom: 30px;
        }

        .btn-them {
            background-color: #d90429;
            color: white;
        }

        .btn-them:hover {
            background-color: #b40322;
        }

        .table thead {
            background-color: #d90429;
            color: white;
        }

        .table td, .table th {
            vertical-align: middle;
        }

        img.avatar {
            width: 60px;
            height: 60px;
            object-fit: cover;
            border-radius: 8px;
        }
    </style>
</head>
<body>

<div class="container">
    <h2>📋 Danh sách cầu thủ đội của bạn</h2>
    <a href="them_cauthu.php" class="btn btn-them mb-3">+ Thêm cầu thủ</a>
    
     <form method="GET" class="mb-3">
    <div class="input-group">
        <input type="text" name="keyword" class="form-control" placeholder="Tìm theo tên, vị trí hoặc số áo..."
               value="<?= isset($_GET['keyword']) ? htmlspecialchars($_GET['keyword']) : '' ?>">
        <button class="btn btn-primary" type="submit">Tìm kiếm</button>
        <a href="cauthu.php" class="btn btn-secondary">Xóa </a>
    </div>
</form>
   

    <div class="table-responsive">
        <table class="table table-bordered align-middle text-center">
            <thead>
                <tr>
                    <th>Ảnh</th>
                    <th>Họ tên</th>
                    <th>Ngày sinh</th>
                    <th>Vị trí</th>
                    <th>Số áo</th>
                    <th>Trạng thái</th>
                    <th>Hành động</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $result->fetch_assoc()) { ?>
                <tr>
                    <td>
                        <?php if (!empty($row['ANH_DAI_DIEN']) && file_exists("../" . $row['ANH_DAI_DIEN'])): ?>
                            <img src="../<?= htmlspecialchars($row['ANH_DAI_DIEN']) ?>" class="avatar" alt="Ảnh cầu thủ">
                        <?php else: ?>
                            <span class="text-muted">Không ảnh</span>
                        <?php endif; ?>
                    </td>
                    <td><?= htmlspecialchars($row['HO_TEN']) ?></td>
                    <td><?= htmlspecialchars($row['NGAY_SINH']) ?></td>
                    <td><?= htmlspecialchars($row['VI_TRI']) ?></td>
                    <td><?= htmlspecialchars($row['SO_AO']) ?></td>
                    <td><?= htmlspecialchars($row['TRANG_THAI']) ?></td>
                    <td>
                        <a href="sua_cauthu.php?id=<?= $row['ID_CAU_THU'] ?>" class="btn btn-sm btn-outline-primary">Sửa</a>
                        <a href="xoa_cauthu.php?id=<?= $row['ID_CAU_THU'] ?>" onclick="return confirm('Xoá cầu thủ này?')" class="btn btn-sm btn-outline-danger">Xoá</a>
                    </td>
                </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>

    <a href="index.php" class="btn btn-secondary mt-3">← Quay lại trang chính</a>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
