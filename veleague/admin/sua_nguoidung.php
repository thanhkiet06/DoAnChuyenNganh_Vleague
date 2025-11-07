<?php
require '../auth.php';
require_role('admin');
require '../connect.php';

// Lấy ID user cần sửa
$id = intval($_GET['id']);
$u = $conn->query("SELECT * FROM NGUOI_DUNG WHERE ID_NGUOI_DUNG = $id")->fetch_assoc();

// Lấy danh sách đội bóng cho HLV/Player
$doibong = $conn->query("
    SELECT * FROM DOI_BONG 
    WHERE HUAN_LUYEN_VIEN IS NULL OR HUAN_LUYEN_VIEN = '{$u['TEN_DANG_NHAP']}'
");

// Khi form được submit
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $ten = trim($_POST['ten']);
    $mk = $_POST['mk'];
    $email = $_POST['email'];
    $sdt = $_POST['sdt'];
    $ngay = $_POST['ngay'];
    $vaitro = $_POST['vaitro'];
    $id_doi = $_POST['id_doi'] ?? null;

    // Nếu người dùng nhập lại mật khẩu mới → hash lại
    if (!empty($mk)) {
        // Nếu nhập mật khẩu trùng với hash cũ, giữ nguyên
        if (!password_verify($mk, $u['MAT_KHAU'])) {
            $hashed_pass = password_hash($mk, PASSWORD_DEFAULT);
        } else {
            $hashed_pass = $u['MAT_KHAU'];
        }
    } else {
        $hashed_pass = $u['MAT_KHAU'];
    }

    // Cập nhật người dùng
    $stmt = $conn->prepare("UPDATE NGUOI_DUNG 
                            SET TEN_DANG_NHAP=?, MAT_KHAU=?, EMAIL=?, SDT=?, NGAY_SINH=?, VAI_TRO=? 
                            WHERE ID_NGUOI_DUNG=?");
    $stmt->bind_param("ssssssi", $ten, $hashed_pass, $email, $sdt, $ngay, $vaitro, $id);
    $stmt->execute();

    // Reset đội nếu người này là HLV cũ
    $conn->query("UPDATE DOI_BONG SET HUAN_LUYEN_VIEN = NULL WHERE HUAN_LUYEN_VIEN = '{$u['TEN_DANG_NHAP']}'");

    // Gán HLV vào đội
    if ($vaitro === 'hlv' && $id_doi) {
        $conn->query("UPDATE DOI_BONG SET HUAN_LUYEN_VIEN = '$ten' WHERE ID_DOI_BONG = $id_doi");
    }

    // Gán Player vào đội (nếu có bảng CAU_THU)
    if ($vaitro === 'player' && $id_doi) {
        $check = $conn->query("SELECT * FROM CAU_THU WHERE ID_NGUOI_DUNG = $id");
        if ($check->num_rows > 0) {
            $conn->query("UPDATE CAU_THU SET ID_DOI_BONG = $id_doi WHERE ID_NGUOI_DUNG = $id");
        } else {
            $stmt2 = $conn->prepare("INSERT INTO CAU_THU (HO_TEN, ID_DOI_BONG, ID_NGUOI_DUNG) VALUES (?, ?, ?)");
            $stmt2->bind_param("sii", $ten, $id_doi, $id);
            $stmt2->execute();
        }
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
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=Inter:wght@400;600&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background-color: #f8f9fa;
            padding: 40px;
        }
        .heading {
            font-family: 'Bebas Neue', sans-serif;
            font-size: 32px;
            color: #2c3e50;
            margin-bottom: 20px;
        }
        .form-label { font-weight: 500; }
    </style>
</head>
<body>

<div class="container col-md-6">
    <h1 class="heading"><i class="bi bi-pencil-fill me-2"></i>Sửa thông tin người dùng</h1>

    <form method="POST" class="bg-white p-4 rounded shadow-sm">
        <div class="mb-3">
            <label class="form-label">Tên đăng nhập</label>
            <input type="text" name="ten" class="form-control" required value="<?= htmlspecialchars($u['TEN_DANG_NHAP']) ?>">
        </div>

        <div class="mb-3">
            <label class="form-label">Mật khẩu (để trống nếu không đổi)</label>
            <input type="password" name="mk" class="form-control" placeholder="Nhập mật khẩu mới (nếu muốn)">
            <div class="form-text text-muted">Hệ thống sẽ tự động mã hoá mật khẩu mới.</div>
        </div>

        <div class="mb-3">
            <label class="form-label">Email</label>
            <input type="email" name="email" class="form-control" required value="<?= htmlspecialchars($u['EMAIL']) ?>">
        </div>

        <div class="mb-3">
            <label class="form-label">Số điện thoại</label>
            <input type="text" name="sdt" class="form-control" value="<?= htmlspecialchars($u['SDT']) ?>">
        </div>

        <div class="mb-3">
            <label class="form-label">Ngày sinh</label>
            <input type="date" name="ngay" class="form-control" value="<?= htmlspecialchars($u['NGAY_SINH']) ?>">
        </div>

        <div class="mb-3">
            <label class="form-label">Vai trò</label>
            <select name="vaitro" class="form-select" required>
                <option value="admin" <?= $u['VAI_TRO'] == 'admin' ? 'selected' : '' ?>>Admin</option>
                <option value="hlv" <?= $u['VAI_TRO'] == 'hlv' ? 'selected' : '' ?>>HLV</option>
                <option value="player" <?= $u['VAI_TRO'] == 'player' ? 'selected' : '' ?>>Player</option>
                <option value="viewer" <?= $u['VAI_TRO'] == 'viewer' ? 'selected' : '' ?>>Viewer</option>
            </select>
        </div>

        <div class="mb-3" id="doibong-group" style="display: none;">
            <label class="form-label">Gán vào đội bóng</label>
            <select name="id_doi" class="form-select">
                <option value="">-- Không thay đổi --</option>
                <?php while ($d = $doibong->fetch_assoc()) { ?>
                    <option value="<?= $d['ID_DOI_BONG'] ?>" 
                        <?= ($d['HUAN_LUYEN_VIEN'] == $u['TEN_DANG_NHAP']) ? 'selected' : '' ?>>
                        <?= htmlspecialchars($d['TEN_DOI_BONG']) ?>
                    </option>
                <?php } ?>
            </select>
        </div>

        <div class="d-flex justify-content-between">
            <a href="nguoidung.php" class="btn btn-secondary"><i class="bi bi-arrow-left"></i> Quay lại</a>
            <button type="submit" class="btn btn-success"><i class="bi bi-check-circle"></i> Cập nhật</button>
        </div>
    </form>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
    const roleSelect = document.querySelector('select[name="vaitro"]');
    const doiGroup = document.getElementById('doibong-group');

    function toggleDoiBong() {
        doiGroup.style.display = (roleSelect.value === 'hlv' || roleSelect.value === 'player') ? 'block' : 'none';
    }

    toggleDoiBong();
    roleSelect.addEventListener('change', toggleDoiBong);
</script>
</body>
</html>
