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

/* Lấy đội của HLV */
$stmt = $conn->prepare("SELECT ID_DOI_BONG FROM DOI_BONG WHERE HUAN_LUYEN_VIEN = ?");
$stmt->bind_param("s", $hlv);
$stmt->execute();
$team = $stmt->get_result()->fetch_assoc();

if (!$team) {
    die("Bạn không có quyền.");
}

$id_doi = $team['ID_DOI_BONG'];

/* Kiểm tra cầu thủ */
$stmt = $conn->prepare("SELECT * FROM CAU_THU WHERE ID_CAU_THU = ? AND ID_DOI_BONG = ?");
$stmt->bind_param("ii", $id, $id_doi);
$stmt->execute();
$player = $stmt->get_result()->fetch_assoc();

if (!$player) {
    die("Không tìm thấy cầu thủ.");
}

/* ===== XỬ LÝ POST ===== */
if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $ho_ten = $_POST['ho_ten'];
    $ngay_sinh = $_POST['ngay_sinh'];
    $vi_tri = $_POST['vi_tri'];
    $so_ao = $_POST['so_ao'];
    $trang_thai = $_POST['trang_thai'];

    $anh_dai_dien = $player['ANH_DAI_DIEN'];

    /* Upload ảnh mới */
    if (!empty($_FILES['anh_dai_dien']['name'])) {

        $file = $_FILES['anh_dai_dien'];
        $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        $allow = ['jpg', 'jpeg', 'png', 'webp'];

        if (!in_array($ext, $allow)) {
            die("Chỉ cho phép ảnh JPG, PNG, WEBP");
        }

        $ten_moi = uniqid('player_') . '.' . $ext;
        $duong_dan = "../uploads/" . $ten_moi;

        if (move_uploaded_file($file['tmp_name'], $duong_dan)) {

            /* Xóa ảnh cũ nếu có */
            if (!empty($anh_dai_dien) && file_exists("../" . $anh_dai_dien)) {
                unlink("../" . $anh_dai_dien);
            }

            /* Lưu đường dẫn chuẩn để hiển thị */
            $anh_dai_dien = "uploads/" . $ten_moi;
        }
    }

    /* Update DB */
    $stmt = $conn->prepare("
        UPDATE CAU_THU 
        SET HO_TEN=?, NGAY_SINH=?, VI_TRI=?, SO_AO=?, TRANG_THAI=?, ANH_DAI_DIEN=? 
        WHERE ID_CAU_THU=? AND ID_DOI_BONG=?
    ");

    $stmt->bind_param(
        "ssssssii",
        $ho_ten,
        $ngay_sinh,
        $vi_tri,
        $so_ao,
        $trang_thai,
        $anh_dai_dien,
        $id,
        $id_doi
    );

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
            <input type="text" name="ho_ten" class="form-control"
                   value="<?= htmlspecialchars($player['HO_TEN']) ?>" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Ngày sinh</label>
            <input type="date" name="ngay_sinh" class="form-control"
                   value="<?= $player['NGAY_SINH'] ?>" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Vị trí</label>
            <input type="text" name="vi_tri" class="form-control"
                   value="<?= $player['VI_TRI'] ?>" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Số áo</label>
            <input type="number" name="so_ao" class="form-control"
                   value="<?= $player['SO_AO'] ?>" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Trạng thái</label>
            <select name="trang_thai" class="form-select">
                <?php
                $list = ['Đang thi đấu', 'Chấn thương', 'Dự bị'];
                foreach ($list as $t) {
                    $sel = $player['TRANG_THAI'] == $t ? 'selected' : '';
                    echo "<option $sel>$t</option>";
                }
                ?>
            </select>
        </div>

        <div class="mb-3">
            <label class="form-label">Ảnh đại diện</label><br>

            <?php if (!empty($player['ANH_DAI_DIEN'])): ?>
                <img src="../<?= $player['ANH_DAI_DIEN'] ?>"
                     style="width:90px;height:90px;border-radius:10px;margin-bottom:10px;"><br>
            <?php endif; ?>

            <input type="file" name="anh_dai_dien" class="form-control">
        </div>

        <button type="submit" class="btn btn-primary">💾 Lưu</button>
        <a href="cauthu.php" class="btn btn-secondary ms-2">⬅ Quay lại</a>
    </form>
</div>

</body>
</html>
