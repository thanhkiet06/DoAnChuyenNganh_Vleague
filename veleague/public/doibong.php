<?php
require '../connect.php';
$result = $conn->query("
    SELECT d.*, g.TEN_GIAI_DAU 
    FROM DOI_BONG d
     JOIN GIAI_DAU g ON d.ID_GIAI_DAU = g.ID_GIAI_DAU
");
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Danh sách đội bóng - V.League</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Bootstrap + Font -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

    <style>
        body {
            font-family: 'Inter', sans-serif;
            background: #f5f6fa;
            margin: 0;
            color: #242424;
            line-height: 1.6; /* tăng readability */
            font-size: 16px; /* chữ lớn hơn */
        }

        .container {
            padding-top: 35px;
            padding-bottom: 50px;
        }

        /* TIÊU ĐỀ */
        .title {
            font-size: 42px;
            font-weight: 700;
            color: #c40c24; 
            letter-spacing: 1px;
            margin-bottom: 35px;
        }

        /* CARD ĐỘI BÓNG */
        .team-card {
            display: flex;
            align-items: center;
            gap: 20px;

            background: #ffffff;
            border-radius: 14px;
            padding: 20px;
            border: 1px solid #ececec;

            transition: 0.25s ease;
            box-shadow: 0 4px 10px rgba(0,0,0,0.05);
        }

        .team-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 8px 18px rgba(0,0,0,0.08);
        }

        /* LOGO */
        .team-logo {
            width: 80px;
            height: 80px;
            object-fit: contain;
            border-radius: 12px;
            border: 1px solid #dedede;
            background: #fafafa;
            padding: 6px;
        }

        /* TÊN ĐỘI + INFO */
        .team-name {
            font-size: 24px;       /* chữ to hơn */
            font-weight: 700;
            color: #1c1c1c;
            margin-bottom: 4px;
        }

        .coach {
            font-size: 17px;
            font-weight: 500;
            color: #3b3b3b;
        }

        .league {
            font-size: 15px;
            color: #6a6a6a;
            margin-top: 3px;
        }

        /* BUTTON */
        .btn-back {
            background-color: #c40c24;
            color: white;
            font-weight: 600;
            border-radius: 10px;
            padding: 10px 26px;
            font-size: 17px;
        }

        .btn-back:hover {
            background-color: #a50a1f;
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

            
                <div>
                    <div class="team-name"><?= $row['TEN_DOI_BONG'] ?></div>
                    <div class="coach">HLV: <?= $row['HUAN_LUYEN_VIEN'] ?></div>
                    <div class="league">Giải: <?= $row['TEN_GIAI_DAU'] ?: 'Không rõ' ?></div>
                </div>

            </div>
        </div>
        <?php } ?>
    </div>

    <div class="text-center mt-5">
        <a href="index.php" class="btn btn-back">← Về trang chính</a>
    </div>
</div>

</body>
</html>
