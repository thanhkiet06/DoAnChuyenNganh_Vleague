<?php
session_start();
require '../connect.php';

// Kiểm tra đăng nhập
if (!isset($_SESSION['user_id']) || $_SESSION['vai_tro'] !== 'player') {
    header("Location: ../login.php");
    exit;
}

$id_cauthu = $_SESSION['user_id'];

// Đếm số tin nhắn chưa đọc
$sql = "
    SELECT COUNT(*) AS SL
    FROM TIN_NHAN_NOI_BO
    WHERE ID_DOI_BONG IN (SELECT ID_DOI_BONG FROM CAU_THU WHERE ID_CAU_THU = $id_cauthu)
      AND (ID_NGUOI_NHAN IS NULL OR ID_NGUOI_NHAN = $id_cauthu)
      AND (DA_XEM = 0)
";
$unread = $conn->query($sql)->fetch_assoc()['SL'];
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Trang cầu thủ</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background:#f7f9fb; font-family: Inter, sans-serif; }
        .container { padding-top:80px; text-align:center; }
        .btn { margin: 10px; }
    </style>
</head>
<body>
<div class="container">
    <h2>Xin chào, <?= htmlspecialchars($_SESSION['ten_dang_nhap']); ?> ⚽</h2>
    <p>Chào mừng bạn đến với khu vực dành cho cầu thủ.</p>

    <a href="messages.php" class="btn btn-danger position-relative">
        💬 Tin nhắn từ HLV
        <?php if ($unread > 0): ?>
            <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-warning text-dark">
                <?= $unread ?>
            </span>
        <?php endif; ?>
    </a>

    <a href="../logout.php" class="btn btn-outline-dark">🚪 Đăng xuất</a>
</div>
</body>
</html>
