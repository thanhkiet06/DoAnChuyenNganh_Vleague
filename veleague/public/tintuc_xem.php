<?php
require '../auth.php';
require '../connect.php';

$ds = $conn->query("SELECT * FROM TIN_TUC ORDER BY NGAY_DANG DESC");
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Tin tức V.League</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .news-card {
            padding: 20px;
            margin-bottom: 20px;
            border: 1px solid #eee;
            border-radius: 10px;
            background: #fff;
            box-shadow: 0 3px 8px rgba(0,0,0,0.03);
        }
        .news-title {
            font-size: 20px;
            font-weight: bold;
            color: #d90429;
        }
        .news-meta {
            font-size: 13px;
            color: #777;
        }
    </style>
</head>
<body class="container py-4">
    <h2 class="mb-4">📰 Tin tức mới nhất</h2>

    <?php while ($tin = $ds->fetch_assoc()): ?>
        <div class="news-card">
            <div class="news-title"><?= htmlspecialchars($tin['TIEU_DE']) ?></div>
            <div class="news-meta">Đăng bởi <?= htmlspecialchars($tin['TAC_GIA']) ?> - <?= date('d/m/Y H:i', strtotime($tin['NGAY_DANG'])) ?></div>
            <p><?= nl2br(htmlspecialchars(mb_substr($tin['NOI_DUNG'], 0, 150))) ?>...</p>
            <a href="tintuc_chitiet.php?id=<?= $tin['ID_TIN'] ?>" class="btn btn-sm btn-outline-primary">Xem chi tiết</a>
        </div>
    <?php endwhile; ?>

    <a href="index.php" class="btn btn-secondary mt-4">← Quay lại</a>
</body>
</html>
