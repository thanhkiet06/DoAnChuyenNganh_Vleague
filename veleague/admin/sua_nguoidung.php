<?php
require '../auth.php';
require_role('admin');
require '../connect.php';

$id = (int)$_GET['id'];
$u = $conn->query("SELECT * FROM NGUOI_DUNG WHERE ID_NGUOI_DUNG = $id")->fetch_assoc();

// Danh sách đội bóng chưa có HLV hoặc đang gán với user này
$doibong = $conn->query("
    SELECT * FROM DOI_BONG 
    WHERE HUAN_LUYEN_VIEN IS NULL 
       OR HUAN_LUYEN_VIEN = '{$u['TEN_DANG_NHAP']}'
");

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $ten    = trim($_POST['ten']);
    $mk     = $_POST['mk'];
    $email  = trim($_POST['email']);
    $sdt    = trim($_POST['sdt']);
    $ngay   = $_POST['ngay'];
    $vaitro = $_POST['vaitro'];
    $id_doi = $_POST['id_doi'] ?? null;

    /* =============================
       XỬ LÝ MẬT KHẨU ĐÚNG CÁCH
       ============================= */

    // Nếu admin KHÔNG đổi mật khẩu → giữ nguyên hash cũ
    if ($mk === '' || password_verify($mk, $u['MAT_KHAU'])) {
        $hash = $u['MAT_KHAU'];
    } else {
        // Nếu nhập mật khẩu mới → hash lại
        $hash = password_hash($mk, PASSWORD_DEFAULT);
    }

    // Cập nhật người dùng
    $stmt = $conn->prepare("
        UPDATE NGUOI_DUNG 
        SET TEN_DANG_NHAP = ?, 
            MAT_KHAU = ?, 
            EMAIL = ?, 
            SDT = ?, 
            NGAY_SINH = ?, 
            VAI_TRO = ?
        WHERE ID_NGUOI_DUNG = ?
    ");
    $stmt->bind_param("ssssssi", $ten, $hash, $email, $sdt, $ngay, $vaitro, $id);
    $stmt->execute();

    // Reset HLV cũ
    $stmt2 = $conn->prepare("
        UPDATE DOI_BONG 
        SET HUAN_LUYEN_VIEN = NULL 
        WHERE HUAN_LUYEN_VIEN = ?
    ");
    $stmt2->bind_param("s", $u['TEN_DANG_NHAP']);
    $stmt2->execute();

    // Gán lại nếu là HLV
    if ($vaitro === 'hlv' && !empty($id_doi)) {
        $stmt3 = $conn->prepare("
            UPDATE DOI_BONG 
            SET HUAN_LUYEN_VIEN = ? 
            WHERE ID_DOI_BONG = ?
        ");
        $stmt3->bind_param("si", $ten, $id_doi);
        $stmt3->execute();
    }

    header("Location: nguoidung.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Sửa người dùng</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container col-md-6 mt-5">
    <h3 class="mb-3">✏️ Sửa người dùng</h3>

    <form method="POST" class="bg-white p-4 rounded shadow-sm">

        <div class="mb-3">
            <label class="form-label">Tên đăng nhập</label>
            <input type="text" name="ten" class="form-control"
                   value="<?= htmlspecialchars($u['TEN_DANG_NHAP']) ?>" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Mật khẩu mới (để trống nếu không đổi)</label>
            <input type="password" name="mk" class="form-control">
        </div>

        <div class="mb-3">
<label class="form-label">Email</label>
            <input type="email" name="email" class="form-control"
                   value="<?= htmlspecialchars($u['EMAIL']) ?>" required>
        </div>

        <div class="mb-3">
            <label class="form-label">SĐT</label>
            <input type="text" name="sdt" class="form-control"
                   value="<?= htmlspecialchars($u['SDT']) ?>">
        </div>

        <div class="mb-3">
            <label class="form-label">Ngày sinh</label>
            <input type="date" name="ngay" class="form-control"
                   value="<?= $u['NGAY_SINH'] ?>">
        </div>

        <div class="mb-3">
            <label class="form-label">Vai trò</label>
            <select name="vaitro" class="form-select" id="role">
                <option value="admin" <?= $u['VAI_TRO']=='admin'?'selected':'' ?>>Admin</option>
                <option value="hlv" <?= $u['VAI_TRO']=='hlv'?'selected':'' ?>>HLV</option>
                <option value="viewer" <?= $u['VAI_TRO']=='viewer'?'selected':'' ?>>Viewer</option>
            </select>
        </div>

        <div class="mb-3" id="doibong-group">
            <label class="form-label">Đội bóng</label>
            <select name="id_doi" class="form-select">
                <option value="">-- Không gán --</option>
                <?php while ($d = $doibong->fetch_assoc()) { ?>
                    <option value="<?= $d['ID_DOI_BONG'] ?>"
                        <?= $d['HUAN_LUYEN_VIEN'] == $u['TEN_DANG_NHAP'] ? 'selected' : '' ?>>
                        <?= $d['TEN_DOI_BONG'] ?>
                    </option>
                <?php } ?>
            </select>
        </div>

        <div class="d-flex justify-content-between">
            <a href="nguoidung.php" class="btn btn-secondary">Quay lại</a>
            <button class="btn btn-success">Cập nhật</button>
        </div>

    </form>
</div>

<script>
const role = document.getElementById('role');
const doi = document.getElementById('doibong-group');

function toggle() {
    doi.style.display = role.value === 'hlv' ? 'block' : 'none';
}
toggle();
role.addEventListener('change', toggle);
</script>

</body>
</html>