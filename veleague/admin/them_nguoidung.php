<?php
require '../auth.php';
require_role('admin');
require '../connect.php';

// Lấy danh sách đội chưa có HLV
$doibong = $conn->query("SELECT ID_DOI_BONG, TEN_DOI_BONG FROM DOI_BONG WHERE HUAN_LUYEN_VIEN IS NULL");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $ten = $_POST['ten'];
    $mk = $_POST['mk'];
    $email = $_POST['email'];
    $sdt = $_POST['sdt'];
    $ngay = $_POST['ngay'];
    $vaitro = $_POST['vaitro'];
    $id_doi = $_POST['id_doi'] ?? null;

    // Tạo người dùng
    $stmt = $conn->prepare("INSERT INTO NGUOI_DUNG (TEN_DANG_NHAP, MAT_KHAU, EMAIL, SDT, NGAY_SINH, VAI_TRO)
                            VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssss", $ten, $mk, $email, $sdt, $ngay, $vaitro);
    $stmt->execute();

    // Gán HLV vào đội nếu có
    if ($vaitro === 'hlv' && $id_doi) {
        $conn->query("UPDATE DOI_BONG SET HUAN_LUYEN_VIEN = '$ten' WHERE ID_DOI_BONG = $id_doi");
    }

    header("Location: nguoidung.php");
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Thêm người dùng</title>
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
        .form-label {
            font-weight: 500;
        }
    </style>
</head>
<body>

<div class="container col-md-6">
    <h1 class="heading"><i class="bi bi-person-plus-fill me-2"></i>Thêm người dùng mới</h1>

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
            <label class="form-label">Số điện thoại</label>
            <input type="text" name="sdt" class="form-control">
        </div>

        <div class="mb-3">
            <label class="form-label">Ngày sinh</label>
            <input type="date" name="ngay" class="form-control">
        </div>

        <div class="mb-3">
            <label class="form-label">Vai trò</label>
            <select name="vaitro" class="form-select" required>
                <option value="">-- Chọn vai trò --</option>
                <option value="admin">Admin</option>
                <option value="hlv">HLV</option>
                <option value="viewer">Viewer</option>
            </select>
        </div>

        <div class="mb-3" id="doibong-group" style="display: none;">
            <label class="form-label">Gán vào đội bóng</label>
            <select name="id_doi" class="form-select">
                <option value="">-- Chọn đội bóng --</option>
                <?php while ($d = $doibong->fetch_assoc()) { ?>
                    <option value="<?= $d['ID_DOI_BONG'] ?>"><?= $d['TEN_DOI_BONG'] ?></option>
                <?php } ?>
            </select>
        </div>

        <div class="d-flex justify-content-between">
            <a href="nguoidung.php" class="btn btn-secondary"><i class="bi bi-arrow-left"></i> Quay lại</a>
            <button type="submit" class="btn btn-primary"><i class="bi bi-check-circle"></i> Thêm người dùng</button>
        </div>
    </form>
</div>

<!-- Bootstrap & JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
    const roleSelect = document.querySelector('select[name="vaitro"]');
    const doiGroup = document.getElementById('doibong-group');

    roleSelect.addEventListener('change', () => {
        doiGroup.style.display = roleSelect.value === 'hlv' ? 'block' : 'none';
    });

    // Hiển thị sẵn nếu reload
    if (roleSelect.value === 'hlv') doiGroup.style.display = 'block';
</script>
</body>
</html>
