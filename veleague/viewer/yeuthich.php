<?php
require '../auth.php';
require_role('viewer');
require '../connect.php';

$user_id = $_SESSION['user_id'];

// Lấy danh sách đội yêu thích của người dùng
$ds = $conn->query("
    SELECT d.*
    FROM DOI_YEU_THICH yt
    JOIN DOI_BONG d ON yt.ID_DOI_BONG = d.ID_DOI_BONG
    WHERE yt.ID_NGUOI_DUNG = $user_id
");
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Đội bóng yêu thích - V.League</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=Inter:wght@400;600&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background-color: #f9fbfd;
        }
        .container {
            padding: 40px 20px;
        }
        .title {
            font-family: 'Bebas Neue', cursive;
            font-size: 42px;
            color: #d90429;
            margin-bottom: 30px;
            text-align: center;
        }
        .team-card {
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba(0,0,0,0.05);
            background: #fff;
            padding: 20px;
            transition: 0.3s;
            height: 100%;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
        }
        .team-card:hover {
            transform: translateY(-5px);
        }
        .team-name {
            font-family: 'Bebas Neue', cursive;
            font-size: 26px;
            color: #222;
            margin-bottom: 5px;
        }
        .btn-back {
            margin-top: 30px;
            background-color: #d90429;
            color: white;
        }
        .btn-back:hover {
            background-color: #b40221;
        }
    </style>
</head>
<body>

<div class="container">
    <h1 class="title">❤️ Các đội bóng bạn yêu thích</h1>

    <div class="row g-4">
        <?php if ($ds->num_rows > 0): ?>
            <?php while ($d = $ds->fetch_assoc()): ?>
                <div class="col-md-4">
                    <div class="team-card text-center">
                        <div class="team-name"><?= htmlspecialchars($d['TEN_DOI_BONG']) ?></div>
                        <div class="small text-muted">HLV: <?= htmlspecialchars($d['HUAN_LUYEN_VIEN']) ?></div>
                        <a href="bo_yeu_thich.php?id=<?= $d['ID_DOI_BONG'] ?>" class="btn btn-outline-danger btn-sm mt-2">💔 Bỏ yêu thích</a>
                    </div>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <div class="text-center text-muted">Bạn chưa yêu thích đội bóng nào.</div>
        <?php endif; ?>
    </div>

    <div class="text-center">
        <a href="index.php" class="btn btn-back px-4 mt-5">← Quay lại trang chính</a>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
