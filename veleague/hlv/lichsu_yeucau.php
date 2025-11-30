<?php
require '../auth.php';
require_role('hlv');
require '../connect.php';

header("Cache-Control: no-cache, must-revalidate");


$id_nguoi_dung = $_SESSION['user_id'];

// Lấy danh sách yêu cầu theo người gửi
$sql = "SELECT * FROM YEU_CAU_USER WHERE ID_NGUOI_DUNG = $id_nguoi_dung ORDER BY NGAY_TAO DESC";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Lịch sử yêu cầu hỗ trợ</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=Inter:wght@400;600&display=swap" rel="stylesheet">

    <style>
        body { background-color: #f7f9fb; font-family: 'Inter', sans-serif; }
        h2 { font-family: 'Bebas Neue', cursive; color: #d90429; font-size: 40px; margin-bottom: 20px; }
        .status {
            padding: 4px 10px;
            border-radius: 6px;
            font-weight: 600;
            color: white;
        }
        .pending { background-color: #ffaa00; }
        .approved { background-color: #28a745; }
        .rejected { background-color: #dc3545; }
    </style>
</head>
<body>

<div class="container mt-4">

    <h2>📜 Lịch sử yêu cầu hỗ trợ</h2>

    <table class="table table-bordered bg-white shadow-sm">
        <thead class="table-dark text-center">
            <tr>
                <th>Loại yêu cầu</th>
                <th>Nội dung</th>
                <th>Ngày gửi</th>
                <th>Trạng thái</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = $result->fetch_assoc()) { ?>
            <tr>
                <td><?= htmlspecialchars($row['LOAI_YEU_CAU']) ?></td>
                <td><?= nl2br(htmlspecialchars($row['NOI_DUNG'])) ?></td>
                <td class="text-center"><?= $row['NGAY_TAO'] ?></td>
                <td class="text-center">
                    <?php
                        if ($row['TRANG_THAI'] == "Chờ duyệt") 
                            echo "<span class='status pending'>⏳ Chờ duyệt</span>";
                        elseif ($row['TRANG_THAI'] == "Đã duyệt") 
                            echo "<span class='status approved'>✔ Đã duyệt</span>";
                        else 
                            echo "<span class='status rejected'>✖ Từ chối</span>";
                    ?>
                </td>
            </tr>
            <?php } ?>
        </tbody>
    </table>

    <a href="gui_yeucau.php" class="btn btn-primary">← Gửi yêu cầu mới</a>
    <a href="index.php" class="btn btn-secondary ms-2">Trang chính</a>

</div>

</body>


</html>
