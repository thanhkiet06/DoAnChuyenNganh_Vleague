<?php
require '../auth.php';
require_role('viewer');
require '../connect.php';

$highlights = $conn->query("
    SELECT h.*, t.NGAY_THI_DAU, d1.TEN_DOI_BONG AS DOI_NHA, d2.TEN_DOI_BONG AS DOI_KHACH
    FROM HIGHLIGHT h
    JOIN TRAN_DAU t ON h.ID_TRAN_DAU = t.ID_TRAN_DAU
    JOIN DOI_BONG d1 ON t.ID_DOI_1 = d1.ID_DOI_BONG
    JOIN DOI_BONG d2 ON t.ID_DOI_2 = d2.ID_DOI_BONG
    ORDER BY t.NGAY_THI_DAU DESC
");

// Hàm chuyển đổi link YouTube
function convertYouTubeLink($url) {
    if (preg_match('/(youtube\.com\/watch\?v=|youtu\.be\/)([a-zA-Z0-9_-]+)/', $url, $matches)) {
        return "https://www.youtube.com/embed/" . $matches[2];
    }
    return $url;
}
?>

<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <title>Highlight trận đấu - V.League 2025</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=Inter:wght@400;600&display=swap"
        rel="stylesheet">
    <style>
    body {
        font-family: 'Inter', sans-serif;
        background-color: #f2f4f8;
    }

    .container {
        padding: 40px 20px;
    }

    .title {
        font-family: 'Bebas Neue', cursive;
        font-size: 44px;
        color: #d90429;
    }

    .highlight-card {
        background: white;
        padding: 15px;
        border-radius: 10px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        margin-bottom: 15px;
    }

    .video-iframe {
        width: 100%;
        height: 300px;
        border-radius: 8px;
    }

    .btn-back {
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
        <h1 class="text-center title">🎥 Highlight trận đấu</h1>
        <p class="text-center text-muted mb-4">Xem lại những khoảnh khắc đáng nhớ của V.League 2025</p>

        <?php if ($highlights->num_rows > 0) { ?>
        <div class="row g-3">
            <?php while ($row = $highlights->fetch_assoc()) { ?>
            <div class="col-12">
                <div class="highlight-card">
                    <h5><?= htmlspecialchars($row['DOI_NHA']) ?> vs <?= htmlspecialchars($row['DOI_KHACH']) ?>
                        (<?= $row['NGAY_THI_DAU'] ?>)</h5>
                    <?php if (filter_var($row['LINK_VIDEO'], FILTER_VALIDATE_URL)) { ?>
                    <iframe class="video-iframe" src="<?= htmlspecialchars(convertYouTubeLink($row['LINK_VIDEO'])) ?>"
                        frameborder="0"
                        allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                        allowfullscreen></iframe>
                    <?php } else { ?>
                    <video class="video-iframe" controls>
                        <source src="../Uploads/<?= htmlspecialchars($row['LINK_VIDEO']) ?>" type="video/mp4">
                        Trình duyệt không hỗ trợ video.
                    </video>
                    <?php } ?>
                </div>
            </div>
            <?php } ?>
        </div>
        <?php } else { ?>
        <p class="text-muted text-center">Chưa có highlight nào.</p>
        <?php } ?>

        <div class="text-center mt-4">
            <a href="index.php" class="btn btn-back px-4">← Về trang chính</a>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>