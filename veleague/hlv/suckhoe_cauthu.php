<?php
require '../auth.php';
require_role('hlv');
require '../connect.php';

$hlv = $_SESSION['ten_dang_nhap'];
$team = $conn->query("SELECT ID_DOI_BONG FROM DOI_BONG WHERE HUAN_LUYEN_VIEN = '$hlv'")->fetch_assoc();

if (!$team) {
    echo "<div class='container mt-5'>
    <p>Bạn chưa có đội bóng.</p>
    <a href='index.php' class='btn btn-secondary mt-2'>Quay lại</a>
    </div>";
    exit;
}

$id_doi = $team['ID_DOI_BONG'];

// Lấy danh sách cầu thủ và tình trạng sức khỏe
$cauthu = $conn->query("
    SELECT c.*, s.ID_SUC_KHOE, s.LOAI_CHAN_THUONG, s.NGAY_CHAN_THUONG, s.THOI_GIAN_HOI_PHUC, s.GHI_CHU
    FROM CAU_THU c
    LEFT JOIN SUC_KHOE_CAU_THU s ON c.ID_CAU_THU = s.ID_CAU_THU
    WHERE c.ID_DOI_BONG = $id_doi
");

// Xử lý thêm/cập nhật tình trạng sức khỏe
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['id_cauthu'])) {
    $id_cauthu = intval($_POST['id_cauthu']);
    $loai_chan_thuong = trim($_POST['loai_chan_thuong']);
    $ngay_chan_thuong = $_POST['ngay_chan_thuong'];
    $thoi_gian_hoi_phuc = $_POST['thoi_gian_hoi_phuc'];
    $ghi_chu = trim($_POST['ghi_chu']);

    // Kiểm tra xem đã có bản ghi sức khỏe chưa
    $existing = $conn->query("SELECT ID_SUC_KHOE FROM SUC_KHOE_CAU_THU WHERE ID_CAU_THU = $id_cauthu")->fetch_assoc();
    if ($existing) {
        $stmt = $conn->prepare("UPDATE SUC_KHOE_CAU_THU SET LOAI_CHAN_THUONG = ?, NGAY_CHAN_THUONG = ?, THOI_GIAN_HOI_PHUC = ?, GHI_CHU = ? WHERE ID_CAU_THU = ?");
        $stmt->bind_param("ssssi", $loai_chan_thuong, $ngay_chan_thuong, $thoi_gian_hoi_phuc, $ghi_chu, $id_cauthu);
    } else {
        $stmt = $conn->prepare("INSERT INTO SUC_KHOE_CAU_THU (ID_CAU_THU, LOAI_CHAN_THUONG, NGAY_CHAN_THUONG, THOI_GIAN_HOI_PHUC, GHI_CHU) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("issss", $id_cauthu, $loai_chan_thuong, $ngay_chan_thuong, $thoi_gian_hoi_phuc, $ghi_chu);
    }
    if ($stmt->execute()) {
        $success = "Lưu thông tin sức khỏe thành công!";
    } else {
        $error = "Lỗi khi lưu thông tin.";
    }
}
?>

<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <title>Sức khỏe cầu thủ - HLV</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=Inter:wght@400;600&display=swap"
        rel="stylesheet">
    <style>
    body {
        font-family: 'Inter', sans-serif;
        background-color: #f7f9fb;
    }

    .container {
        padding: 40px 20px;
        max-width: 1100px;
    }

    h2 {
        font-family: 'Bebas Neue', cursive;
        font-size: 40px;
        color: #d90429;
        margin-bottom: 30px;
    }

    .player-card {
        background: white;
        padding: 20px;
        border-radius: 12px;
        box-shadow: 0 6px 14px rgba(0, 0, 0, 0.08);
        margin-bottom: 20px;
    }

    .btn-save {
        background-color: #d90429;
        color: white;
    }

    .btn-save:hover {
        background-color: #b40322;
    }
    </style>
</head>

<body>
    <div class="container">
        <h2>🏥 Sức khỏe cầu thủ</h2>

        <?php if (isset($success)) { ?>
        <div class="alert alert-success"><?= $success ?></div>
        <?php } elseif (isset($error)) { ?>
        <div class="alert alert-danger"><?= $error ?></div>
        <?php } ?>

        <?php while ($row = $cauthu->fetch_assoc()) { ?>
        <div class="player-card">
            <h5><?= htmlspecialchars($row['HO_TEN']) ?> (#<?= $row['SO_AO'] ?>)</h5>
            <form method="POST">
                <input type="hidden" name="id_cauthu" value="<?= $row['ID_CAU_THU'] ?>">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">Loại chấn thương</label>
                        <input type="text" name="loai_chan_thuong" class="form-control"
                            value="<?= htmlspecialchars($row['LOAI_CHAN_THUONG'] ?? '') ?>"
                            placeholder="VD: Chấn thương đầu gối">
                    </div>

                    <div class="col-md-3">
                        <label class="form-label">Ngày chấn thương</label>
                        <input type="date" name="ngay_chan_thuong" class="form-control"
                            value="<?= $row['NGAY_CHAN_THUONG'] ?? '' ?>">
                    </div>

                    <div class="col-md-3">
                        <label class="form-label">Thời gian hồi phục</label>
                        <input type="date" name="thoi_gian_hoi_phuc" class="form-control"
                            value="<?= $row['THOI_GIAN_HOI_PHUC'] ?? '' ?>">
                    </div>

                    <div class="col-12">
                        <label class="form-label">Ghi chú</label>
                        <textarea name="ghi_chu" class="form-control" rows="3"
                            placeholder="VD: Cần nghỉ 2 tuần, theo dõi bởi bác sĩ..."><?= htmlspecialchars($row['GHI_CHU'] ?? '') ?></textarea>
                    </div>

                    <div class="col-12">
                        <button type="submit" class="btn btn-save">Lưu thông tin</button>
                    </div>

                </div>
            </form>
        </div>
        <?php } ?>

        <a href="index.php" class="btn btn-secondary mt-3">← Quay lại trang chính</a>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>