<?php
require '../auth.php';
require '../connect.php';

$id = $_GET['id'];
$tin = $conn->query("SELECT * FROM TIN_TUC WHERE ID_TIN = $id")->fetch_assoc();

if (!$tin) {
    echo "Không tìm thấy bài viết.";
    exit;
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title><?= htmlspecialchars($tin['TIEU_DE']) ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="container py-4">
    <h2 class="mb-2"><?= htmlspecialchars($tin['TIEU_DE']) ?></h2>
    <div class="text-muted mb-4">Đăng bởi <?= htmlspecialchars($tin['TAC_GIA']) ?> lúc <?= date('d/m/Y H:i', strtotime($tin['NGAY_DANG'])) ?></div>
    <div><?= nl2br(htmlspecialchars($tin['NOI_DUNG'])) ?></div>
    <a href="tintuc_xem.php" class="btn btn-outline-secondary mt-4">← Quay lại danh sách</a>
</body>
</html>
