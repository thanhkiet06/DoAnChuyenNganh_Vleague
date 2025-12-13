<?php
require '../connect.php';

$keyword = $_GET['q'] ?? '';
$vitri = $_GET['vitri'] ?? '';

$doibong_kq = [];
$cauthu_kq = [];

if ($keyword != '') {
    // Tìm đội bóng theo tên đội hoặc HLV
    $doibong_kq = $conn->query("
        SELECT * FROM DOI_BONG 
        WHERE TEN_DOI_BONG LIKE '%$keyword%' 
           OR HUAN_LUYEN_VIEN LIKE '%$keyword%'
    ");

    // Tìm cầu thủ theo tên + vị trí
    $sql_cauthu = "
        SELECT c.*, d.TEN_DOI_BONG, d.LOGO 
        FROM CAU_THU c 
        JOIN DOI_BONG d ON c.ID_DOI_BONG = d.ID_DOI_BONG 
        WHERE c.HO_TEN LIKE '%$keyword%'
    ";
    if ($vitri != '') {
        $sql_cauthu .= " AND c.VI_TRI = '$vitri'";
    }

    $cauthu_kq = $conn->query($sql_cauthu);
}
?>
<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <title>Tìm kiếm đội bóng hoặc cầu thủ - V.League</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

    <style>
        /* =======================
           STYLE CHỮ DỄ ĐỌC 
           ======================= */
        body {
            font-family: 'Inter', sans-serif;
            background-color: #f5f7fa;
            color: #222;
            font-size: 17px;
            line-height: 1.65;
            margin: 0;
        }

        .container {
            padding: 40px 20px;
            max-width: 960px;
        }

        .title {
            font-size: 40px;
            font-weight: 700;
            text-align: center;
            margin-bottom: 35px;
            color: #c80e2a;
            letter-spacing: 1px;
        }

        /* Form tìm kiếm */
        .form-control,
        .form-select {
            font-size: 17px;
            padding: 12px 14px;
            border-radius: 10px;
            border: 1px solid #d3d8e0;
            transition: .2s;
        }

        .form-control:focus,
        .form-select:focus {
            border-color: #c80e2a;
            box-shadow: 0 0 0 3px rgba(200, 14, 42, 0.15);
        }

        .btn-search {
            background-color: #c80e2a;
            color: white;
            font-size: 17px;
            padding: 12px 0;
            font-weight: 600;
            border-radius: 10px;
        }

        .btn-search:hover {
            background-color: #a20b24;
        }

        /* Box kết quả */
        .result-box {
            background: white;
            border-radius: 14px;
            padding: 24px;
            margin-top: 30px;
            box-shadow: 0 6px 18px rgba(0, 0, 0, 0.06);
        }

        .result-box h4 {
            font-size: 26px;
            font-weight: 700;
            color: #333;
            margin-bottom: 15px;
        }

        .item-row {
            display: flex;
            align-items: center;
            padding: 12px 0;
            border-bottom: 1px solid #f0f0f0;
            gap: 12px;
        }

        .item-row:last-child {
            border-bottom: none;
        }

        .avatar,
        .logo {
            width: 60px;
            height: 60px;
            border-radius: 12px;
            object-fit: cover;
            background: #eee;
            border: 1px solid #ddd;
        }

        /* Nút quay lại */
        .btn-back {
            margin-top: 45px;
            background-color: #c80e2a;
            color: white;
            font-size: 17px;
            padding: 12px 28px;
            border-radius: 10px;
            font-weight: 600;
        }

        .btn-back:hover {
            background-color: #a20b24;
        }
    </style>
</head>

<body>

    <div class="container">

        <h1 class="title">🔍 Tìm kiếm đội bóng hoặc cầu thủ</h1>

        <!-- FORM TÌM KIẾM -->
        <form method="GET" class="row g-3 mb-4">
            <div class="col-md-6">
                <input type="text" name="q" class="form-control"
                    value="<?= htmlspecialchars($keyword) ?>"
                    placeholder="Nhập tên đội, cầu thủ hoặc HLV..." required>
            </div>

            <div class="col-md-3">
                <select name="vitri" class="form-select">
                    <option value="">-- Tất cả vị trí --</option>
                    <option value="Thủ môn" <?= $vitri=='Thủ môn'?'selected':'' ?>>Thủ môn</option>
                    <option value="Hậu vệ" <?= $vitri=='Hậu vệ'?'selected':'' ?>>Hậu vệ</option>
                    <option value="Tiền vệ" <?= $vitri=='Tiền vệ'?'selected':'' ?>>Tiền vệ</option>
                    <option value="Tiền đạo" <?= $vitri=='Tiền đạo'?'selected':'' ?>>Tiền đạo</option>
                </select>
            </div>

            <div class="col-md-3">
                <button class="btn btn-search w-100">Tìm kiếm</button>
            </div>
        </form>

        <?php if ($keyword != ''): ?>
        <div class="row">

            <!-- KẾT QUẢ CẦU THỦ -->
            <div class="col-md-6">
                <div class="result-box">
                    <h4>Cầu thủ:</h4>

                    <?php if ($cauthu_kq && $cauthu_kq->num_rows > 0): ?>
                        <?php while ($c = $cauthu_kq->fetch_assoc()): ?>
                            <div class="item-row">
                                <?php if (!empty($c['ANH_DAI_DIEN'])): ?>
                                    <img src="<?= (str_starts_with($c['ANH_DAI_DIEN'], 'http') ? $c['ANH_DAI_DIEN'] : '../'.$c['ANH_DAI_DIEN']) ?>"
                                         class="avatar">
                                <?php endif; ?>

                                <div>
                                    <strong style="font-size: 18px;"><?= $c['HO_TEN'] ?></strong><br>
                                    <span style="color:#555;"><?= $c['VI_TRI'] ?></span><br>
                                    <small style="color:#777;"><?= $c['TEN_DOI_BONG'] ?></small>
                                </div>
                            </div>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <p class="text-muted">Không tìm thấy cầu thủ nào.</p>
                    <?php endif; ?>
                </div>
            </div>

        </div>
        <?php endif; ?>

        <!-- Nút quay lại -->
        <div class="text-center">
            <a href="index.php" class="btn btn-back">← Về trang chính</a>
        </div>

    </div>

</body>
</html>
