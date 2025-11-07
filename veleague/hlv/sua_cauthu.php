<?php
require '../auth.php';
require_role('hlv');
require '../connect.php';

if (!isset($_GET['id'])) {
    header("Location: cauthu.php");
    exit;
}

$id = intval($_GET['id']);
$hlv = $_SESSION['ten_dang_nhap'];

// Lấy thông tin đội của HLV
$team = $conn->query("SELECT ID_DOI_BONG FROM DOI_BONG WHERE HUAN_LUYEN_VIEN = '$hlv'")->fetch_assoc();
if (!$team) {
    echo "Bạn không có quyền sửa cầu thủ.";
    exit;
}

$id_doi = $team['ID_DOI_BONG'];

// Kiểm tra cầu thủ có thuộc đội không
$player = $conn->query("SELECT * FROM CAU_THU WHERE ID_CAU_THU = $id AND ID_DOI_BONG = $id_doi")->fetch_assoc();
if (!$player) {
    echo "Không tìm thấy cầu thủ.";
    exit;
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $ho_ten = $_POST['ho_ten'];
    $ngay_sinh = $_POST['ngay_sinh'];
    $vi_tri = $_POST['vi_tri'];
    $so_ao = $_POST['so_ao'];
    $trang_thai = $_POST['trang_thai'];
    $anh_moi = $_FILES['anh_dai_dien'];

    $anh_dai_dien = $player['ANH_DAI_DIEN']; // giữ ảnh cũ mặc định

    if ($anh_moi['error'] === 0 && $anh_moi['size'] > 0) {
        $ten_anh = uniqid('img_') . '_' . basename($anh_moi['name']);
        $duong_dan = '../uploads/' . $ten_anh;

        if (move_uploaded_file($anh_moi['tmp_name'], $duong_dan)) {
            $anh_dai_dien = $duong_dan;
        }
    }

    $stmt = $conn->prepare("UPDATE CAU_THU SET HO_TEN=?, NGAY_SINH=?, VI_TRI=?, SO_AO=?, TRANG_THAI=?, ANH_DAI_DIEN=? WHERE ID_CAU_THU=? AND ID_DOI_BONG=?");
    $stmt->bind_param("ssssssii", $ho_ten, $ngay_sinh, $vi_tri, $so_ao, $trang_thai, $anh_dai_dien, $id, $id_doi);
    $stmt->execute();

    header("Location: cauthu.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Sửa cầu thủ</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container mt-5">
    <h2 class="mb-4">✏️ Sửa thông tin cầu thủ</h2>
    <form method="POST" enctype="multipart/form-data">
        <div class="mb-3">
            <label class="form-label">Họ tên</label>
            <input type="text" name="ho_ten" class="form-control" value="<?= htmlspecialchars($player['HO_TEN']) ?>" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Ngày sinh</label>
            <input type="date" name="ngay_sinh" class="form-control" value="<?= $player['NGAY_SINH'] ?>" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Vị trí</label>
            <input type="text" name="vi_tri" class="form-control" value="<?= $player['VI_TRI'] ?>" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Số áo</label>
            <input type="number" name="so_ao" class="form-control" value="<?= $player['SO_AO'] ?>" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Trạng thái</label>
            <select name="trang_thai" class="form-select" required>
                <option value="Đang thi đấu" <?= $player['TRANG_THAI'] == 'Đang thi đấu' ? 'selected' : '' ?>>Đang thi đấu</option>
                <option value="Chấn thương" <?= $player['TRANG_THAI'] == 'Chấn thương' ? 'selected' : '' ?>>Chấn thương</option>
                <option value="Dự bị" <?= $player['TRANG_THAI'] == 'Dự bị' ? 'selected' : '' ?>>Dự bị</option>
            </select>
        </div>
        <div class="mb-3">
            <label class="form-label">Ảnh đại diện (tùy chọn)</label><br>
            <?php if (!empty($player['ANH_DAI_DIEN'])): ?>
                <img src="<?= $player['ANH_DAI_DIEN'] ?>" alt="Ảnh cũ" style="width:80px;height:80px;border-radius:8px;margin-bottom:10px;"><br>
            <?php endif; ?>
            <input type="file" name="anh_dai_dien" class="form-control">
        </div>
        <button type="submit" class="btn btn-primary">Lưu thay đổi</button>
        <a href="cauthu.php" class="btn btn-secondary ms-2">Quay lại</a>
    </form>
</div>

</body>
</html>