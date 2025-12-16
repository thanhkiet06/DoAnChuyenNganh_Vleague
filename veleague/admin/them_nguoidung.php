<?php
require '../auth.php';
require_role('admin');
require '../connect.php';

// Lấy danh sách đội chưa có HLV
$doibong = $conn->query(
    "SELECT ID_DOI_BONG, TEN_DOI_BONG 
     FROM DOI_BONG 
     WHERE HUAN_LUYEN_VIEN IS NULL"
);

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $ten    = trim($_POST['ten']);
    $mk     = $_POST['mk'];
    $email  = trim($_POST['email']);
    $sdt    = trim($_POST['sdt']);
    $ngay   = $_POST['ngay'];
    $vaitro = $_POST['vaitro'];
    $id_doi = $_POST['id_doi'] ?? null;

    // ✅ HASH MẬT KHẨU (CỰC KỲ QUAN TRỌNG)
    $hash = password_hash($mk, PASSWORD_DEFAULT);

    // Thêm người dùng
    $stmt = $conn->prepare(
        "INSERT INTO NGUOI_DUNG 
        (TEN_DANG_NHAP, MAT_KHAU, EMAIL, SDT, NGAY_SINH, VAI_TRO)
        VALUES (?, ?, ?, ?, ?, ?)"
    );

    $stmt->bind_param("ssssss", $ten, $hash, $email, $sdt, $ngay, $vaitro);
    $stmt->execute();

    // Gán HLV vào đội
    if ($vaitro === 'hlv' && !empty($id_doi)) {
        $stmt2 = $conn->prepare(
            "UPDATE DOI_BONG 
             SET HUAN_LUYEN_VIEN = ? 
             WHERE ID_DOI_BONG = ?"
        );
        $stmt2->bind_param("si", $ten, $id_doi);
        $stmt2->execute();
    }

    header("Location: nguoidung.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Thêm người dùng</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container col-md-6 mt-5">
    <h3 class="mb-3"><i class="bi bi-person-plus-fill"></i> Thêm người dùng</h3>

    <form method="POST" class="bg-white p-4 rounded shadow-sm">

        <div class="mb-3">
            <label class="form-label">Tên đăng nhập</label>
            <input type="text" name="ten" class="form-control" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Mật khẩu</label>
            <input type="text" name="mk" class="form-control" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Email</label>
            <input type="email" name="email" class="form-control" required>
        </div>

        <div class="mb-3">
            <label class="form-label">SĐT</label>
            <input type="text" name="sdt" class="form-control">
        </div>

        <div class="mb-3">
            <label class="form-label">Ngày sinh</label>
            <input type="date" name="ngay" class="form-control">
        </div>

        <div class="mb-3">
            <label class="form-label">Vai trò</label>
            <select name="vaitro" class="form-select" required id="role">
                <option value="">-- Chọn --</option>
                <option value="admin">Admin</option>
                <option value="hlv">HLV</option>
                <option value="viewer">Viewer</option>
            </select>
        </div>

        <div class="mb-3" id="doibong" style="display:none;">
            <label class="form-label">Đội bóng</label>
            <select name="id_doi" class="form-select">
                <option value="">-- Chọn đội --</option>
                <?php while ($d = $doibong->fetch_assoc()) { ?>
                    <option value="<?= $d['ID_DOI_BONG'] ?>">
                        <?= $d['TEN_DOI_BONG'] ?>
                    </option>
                <?php } ?>
            </select>
        </div>

        <div class="d-flex justify-content-between">
            <a href="nguoidung.php" class="btn btn-secondary">Quay lại</a>
            <button class="btn btn-primary">Thêm</button>
        </div>
    </form>
</div>

<script>
document.getElementById('role').addEventListener('change', function () {
    document.getElementById('doibong').style.display =
        this.value === 'hlv' ? 'block' : 'none';
});
</script>

</body>
</html>