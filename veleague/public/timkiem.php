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

    // Tìm cầu thủ theo tên và vị trí (nếu có chọn)
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
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=Inter:wght@400;600&display=swap"
        rel="stylesheet">
    <style>
    body {
        font-family: 'Inter', sans-serif;
        background-color: #f5f7fa;
    }

    .container {
        padding: 40px 20px;
        max-width: 960px;
    }

    .title {
        font-family: 'Bebas Neue', cursive;
        font-size: 42px;
        color: #d90429;
        text-align: center;
        margin-bottom: 30px;
    }

    .form-control {
        font-size: 18px;
    }

    .btn-search {
        background-color: #d90429;
        color: white;
        font-weight: 500;
    }

    .btn-search:hover {
        background-color: #b40221;
    }

    .result-box {
        background-color: white;
        border-radius: 10px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
        padding: 20px;
        margin-top: 30px;
    }

    .result-box h4 {
        font-family: 'Bebas Neue', cursive;
        font-size: 28px;
        margin-bottom: 15px;
    }

    .logo,
    .avatar {
        width: 48px;
        height: 48px;
        object-fit: cover;
        border-radius: 8px;
        margin-right: 10px;
    }

    .item-row {
        display: flex;
        align-items: center;
        margin-bottom: 10px;
    }

    .btn-back {
        margin-top: 40px;
        background-color: #d90429;
        color: white;
    }
    </style>
</head>

<body>

    <div class="container">
        <h1 class="title">🔍 Tìm kiếm đội bóng hoặc cầu thủ</h1>

        <form method="GET" class="row g-2 mb-4">
            <div class="col-md-6">
                <input type="text" name="q" class="form-control" value="<?= htmlspecialchars($keyword) ?>"
                    placeholder="Nhập tên đội/cầu thủ/HLV..." required>
            </div>
            <div class="col-md-3">
                <select name="vitri" class="form-select">
                    <option value="">-- Tất cả vị trí --</option>
                    <option value="Thủ môn" <?= $vitri == 'Thủ môn' ? 'selected' : '' ?>>Thủ môn</option>
                    <option value="Hậu vệ" <?= $vitri == 'Hậu vệ' ? 'selected' : '' ?>>Hậu vệ</option>
                    <option value="Tiền vệ" <?= $vitri == 'Tiền vệ' ? 'selected' : '' ?>>Tiền vệ</option>
                    <option value="Tiền đạo" <?= $vitri == 'Tiền đạo' ? 'selected' : '' ?>>Tiền đạo</option>
                </select>
            </div>
            <div class="col-md-3">
                <button type="submit" class="btn btn-search w-100">Tìm kiếm</button>
            </div>
        </form>

        <?php if ($keyword != ''): ?>
        <div class="row">


            <!-- Đội bóng -->
            <!-- phần này đã bị comment lại -->

            <!-- <div class="col-md-6">                             
            <div class="result-box">
                <h4>Đội bóng:</h4>
                <?php if ($doibong_kq && $doibong_kq->num_rows > 0): ?>
                    <?php while ($d = $doibong_kq->fetch_assoc()): ?>
                        <div class="item-row">
                            <?php if (!empty($d['LOGO'])): ?>
                                <img src="<?= (str_starts_with($d['LOGO'], 'http') ? $d['LOGO'] : '../' . $d['LOGO']) ?>" class="logo" alt="logo">
                            <?php endif; ?>
                            <div>
                                <strong><?= $d['TEN_DOI_BONG'] ?></strong><br>
                                <small>HLV: <?= $d['HUAN_LUYEN_VIEN'] ?></small>
                            </div>
                        </div>
                    <?php endwhile; ?>
                <?php else: ?>
                    <p class="text-muted">Không tìm thấy đội bóng nào.</p>
                <?php endif; ?>
            </div>
        </div>  -->



            <!-- Cầu thủ -->
            <div class="col-md-6">
                <div class="result-box">
                    <h4>Cầu thủ:</h4>
                    <?php if ($cauthu_kq && $cauthu_kq->num_rows > 0): ?>
                    <?php while ($c = $cauthu_kq->fetch_assoc()): ?>
                    <div class="item-row">
                        <?php if (!empty($c['ANH_DAI_DIEN'])): ?>
                        <img src="<?= (str_starts_with($c['ANH_DAI_DIEN'], 'http') ? $c['ANH_DAI_DIEN'] : '../' . $c['ANH_DAI_DIEN']) ?>"
                            class="avatar" alt="avatar">
                        <?php endif; ?>
                        <div>
                            <strong><?= $c['HO_TEN'] ?></strong> - <?= $c['VI_TRI'] ?><br>
                            <small><?= $c['TEN_DOI_BONG'] ?></small>
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

        <div class="text-center">
            <a href="index.php" class="btn btn-back px-4">← Về trang chính</a>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>