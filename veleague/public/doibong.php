<?php
require '../connect.php';
$result = $conn->query("SELECT d.*, g.TEN_GIAI_DAU 
                        FROM DOI_BONG d 
                        LEFT JOIN GIAI_DAU g ON d.ID_GIAI_DAU = g.ID_GIAI_DAU");
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Danh sách đội bóng - V.League</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Bootstrap & Fonts -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=Inter:wght@400;600&display=swap" rel="stylesheet">

    <style>
        body {
            font-family: 'Inter', sans-serif;
            background-color: #f9fbfd;
        }

        .container {
            padding-top: 40px;
            padding-bottom: 60px;
        }

        .title {
            font-family: 'Bebas Neue', cursive;
            font-size: 44px;
            color: #d90429;
            margin-bottom: 30px;
        }

        .team-card {
            border: none;
            border-left: 6px solid #d90429;
            border-radius: 10px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.07);
            padding: 20px;
            background: white;
            transition: 0.3s;
        }

        .team-card:hover {
            transform: translateY(-3px);
            background-color: #fff3f3;
        }

        .team-name {
            font-family: 'Bebas Neue', cursive;
            font-size: 26px;
            color: #1e1e2f;
        }

        .coach {
            font-weight: 500;
            color: #333;
        }

        .league {
            font-size: 14px;
            color: #888;
        }

        .btn-back {
            background-color: #d90429;
            color: white;
            font-weight: 500;
        }

        .btn-back:hover {
            background-color: #b40221;
        }
    </style>
</head>
<body>

<div class="container">
    <h1 class="text-center title">📋 Danh sách các đội bóng</h1>

    <div class="row row-cols-1 row-cols-md-2 g-4">
        <?php while ($row = $result->fetch_assoc()) { ?>
        <div class="col">
            <div class="team-card">
                <div class="team-name"><?= $row['TEN_DOI_BONG'] ?></div>
                <div class="coach">HLV: <?= $row['HUAN_LUYEN_VIEN'] ?></div>
                <div class="league">Giải: <?= $row['TEN_GIAI_DAU'] ?: 'Không rõ' ?></div>
            </div>
        </div>
        <?php } ?>
    </div>

    <div class="text-center mt-5">
        <a href="index.php" class="btn btn-back px-4">← Về trang chính</a>
    </div>
</div>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
