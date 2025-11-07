<?php
require '../auth.php';
require_role('hlv');
require '../connect.php';

$hlv = $_SESSION['ten_dang_nhap'];
$team = $conn->query("SELECT ID_DOI_BONG FROM DOI_BONG WHERE HUAN_LUYEN_VIEN = '$hlv'")->fetch_assoc();

if (!$team) {
    echo    "<div class='container mt-5'>
             <p>Bạn chưa có đội bóng.</p>
             <a href='index.php' class='btn btn-secondary mt-2'>Quay lại</a>
        </div>";
    exit;
}

$id_doi = $team['ID_DOI_BONG'];

// Lấy danh sách trận đấu sắp tới và đã diễn ra
$trandau = $conn->query("
    SELECT t.*, d1.TEN_DOI_BONG AS DOI_NHA, d2.TEN_DOI_BONG AS DOI_KHACH
    FROM TRAN_DAU t
    JOIN DOI_BONG d1 ON t.ID_DOI_1 = d1.ID_DOI_BONG
    JOIN DOI_BONG d2 ON t.ID_DOI_2 = d2.ID_DOI_BONG
    WHERE t.ID_DOI_1 = $id_doi OR t.ID_DOI_2 = $id_doi
    ORDER BY t.NGAY_THI_DAU DESC
");

// Lấy danh sách cầu thủ
$cauthu = $conn->query("SELECT ID_CAU_THU, HO_TEN, VI_TRI FROM CAU_THU WHERE ID_DOI_BONG = $id_doi");

// Xử lý lưu đội hình
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['id_tran'])) {
    $id_tran = intval($_POST['id_tran']);
    $cau_thu_chinh = isset($_POST['cau_thu_chinh']) ? implode(',', $_POST['cau_thu_chinh']) : '';
    $cau_thu_du_bi = isset($_POST['cau_thu_du_bi']) ? implode(',', $_POST['cau_thu_du_bi']) : '';
    $ghi_chu = trim($_POST['ghi_chu']);

    // Kiểm tra xem đã có đội hình chưa
    $existing = $conn->query("SELECT ID_DOI_HINH FROM DOI_HINH_THI_DAU WHERE ID_TRAN_DAU = $id_tran AND ID_DOI_BONG = $id_doi")->fetch_assoc();
    if ($existing) {
        $stmt = $conn->prepare("UPDATE DOI_HINH_THI_DAU SET CAU_THU_CHINH = ?, CAU_THU_DU_BI = ?, GHI_CHU = ? WHERE ID_TRAN_DAU = ? AND ID_DOI_BONG = ?");
        $stmt->bind_param("sssii", $cau_thu_chinh, $cau_thu_du_bi, $ghi_chu, $id_tran, $id_doi);
    } else {
        $stmt = $conn->prepare("INSERT INTO DOI_HINH_THI_DAU (ID_TRAN_DAU, ID_DOI_BONG, CAU_THU_CHINH, CAU_THU_DU_BI, GHI_CHU) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("iisss", $id_tran, $id_doi, $cau_thu_chinh, $cau_thu_du_bi, $ghi_chu);
    }
    if ($stmt->execute()) {
        $success = "Lưu đội hình thành công!";
    } else {
        $error = "Lỗi khi lưu đội hình.";
    }
}
?>

<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <title>Đội hình thi đấu - HLV</title>
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

    .match-card {
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
        <h2>⚽ Đội hình thi đấu</h2>

        <?php if (isset($success)) { ?>
        <div class="alert alert-success"><?= $success ?></div>
        <?php } elseif (isset($error)) { ?>
        <div class="alert alert-danger"><?= $error ?></div>
        <?php } ?>

        <?php while ($row = $trandau->fetch_assoc()) { 
            // Lấy ghi chú đội hình nếu đã tồn tại
            $doihinh = $conn->query("SELECT GHI_CHU FROM DOI_HINH_THI_DAU WHERE ID_TRAN_DAU = {$row['ID_TRAN_DAU']} AND ID_DOI_BONG = $id_doi")->fetch_assoc();
            $ghi_chu_da_luu = $doihinh ? htmlspecialchars($doihinh['GHI_CHU']) : '';
        ?>
        <div class="match-card">
            <h5><?= htmlspecialchars($row['DOI_NHA']) ?> vs <?= htmlspecialchars($row['DOI_KHACH']) ?>
                (<?= $row['NGAY_THI_DAU'] ?>)</h5>
            <?php if ($ghi_chu_da_luu): ?>
            <p><strong>Ghi chú đã lưu:</strong> <?= $ghi_chu_da_luu ?></p>
            <?php endif; ?>

            <form method="POST">
                <input type="hidden" name="id_tran" value="<?= $row['ID_TRAN_DAU'] ?>">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">Cầu thủ chính</label>
                        <select name="cau_thu_chinh[]" class="form-select" multiple size="5">
                            <?php
                            $cauthu->data_seek(0);
                            while ($player = $cauthu->fetch_assoc()) {
                                echo "<option value='{$player['ID_CAU_THU']}'>" . htmlspecialchars($player['HO_TEN']) . " ({$player['VI_TRI']})</option>";
                            }
                            ?>
                        </select>
                        <small class="form-text text-muted">Chọn 11 cầu thủ chính (giữ Ctrl để chọn nhiều).</small>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Cầu thủ dự bị</label>
                        <select name="cau_thu_du_bi[]" class="form-select" multiple size="5">
                            <?php
                            $cauthu->data_seek(0);
                            while ($player = $cauthu->fetch_assoc()) {
                                echo "<option value='{$player['ID_CAU_THU']}'>" . htmlspecialchars($player['HO_TEN']) . " ({$player['VI_TRI']})</option>";
                            }
                            ?>
                        </select>
                    </div>
                    <div class="col-12">
                        <label class="form-label">Ghi chú (VD: Sơ đồ chiến thuật)</label>
                        <textarea name="ghi_chu" class="form-control" rows="3"
                            placeholder="VD: Sơ đồ 4-4-2, tập trung tấn công cánh phải..."><?= $ghi_chu_da_luu ?></textarea>
                    </div>
                    <div class="col-12">
                        <button type="submit" class="btn btn-save">Lưu đội hình</button>
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