<?php
require '../auth.php';
require_role('admin');
require '../connect.php';

$giaidau = $conn->query("SELECT * FROM GIAI_DAU");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $ten = $_POST['ten'];
    $hlv = $_POST['hlv'];
    $giai = $_POST['giai'];
    $logo_path = null;

    // Xử lý upload logo
    if (isset($_FILES['logo']) && $_FILES['logo']['error'] === UPLOAD_ERR_OK) {
        $ext = pathinfo($_FILES['logo']['name'], PATHINFO_EXTENSION);
        $allowed = ['jpg', 'jpeg', 'png'];
        if (in_array(strtolower($ext), $allowed)) {
            $folder = '../uploads/logo/';
            if (!file_exists($folder)) {
                mkdir($folder, 0777, true);
            }
            $filename = uniqid('logo_') . '.' . $ext;
            move_uploaded_file($_FILES['logo']['tmp_name'], $folder . $filename);
            $logo_path = 'uploads/logo/' . $filename;
        }
    }

    // Lưu vào DB
    $stmt = $conn->prepare("INSERT INTO DOI_BONG (TEN_DOI_BONG, HUAN_LUYEN_VIEN, ID_GIAI_DAU, LOGO) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssis", $ten, $hlv, $giai, $logo_path);
    $stmt->execute();
    header("Location: doibong.php");
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Thêm đội bóng</title>
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
    <h1 class="heading"><i class="bi bi-plus-circle me-2"></i>Thêm đội bóng mới</h1>

    <form method="POST" enctype="multipart/form-data" class="bg-white p-4 rounded shadow-sm">
        <div class="mb-3">
            <label class="form-label">Tên đội</label>
            <input type="text" name="ten" class="form-control" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Huấn luyện viên</label>
            <input type="text" name="hlv" class="form-control" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Giải đấu</label>
            <select name="giai" class="form-select" required>
                <?php while ($g = $giaidau->fetch_assoc()) { ?>
                    <option value="<?= $g['ID_GIAI_DAU'] ?>"><?= $g['TEN_GIAI_DAU'] ?></option>
                <?php } ?>
            </select>
        </div>

        <div class="mb-3">
            <label class="form-label">Logo đội (file ảnh)</label>
            <input type="file" name="logo" class="form-control" accept=".jpg,.jpeg,.png">
        </div>

        <div class="d-flex justify-content-between">
            <a href="doibong.php" class="btn btn-secondary"><i class="bi bi-arrow-left"></i> Quay lại</a>
            <button type="submit" class="btn btn-primary"><i class="bi bi-check-circle"></i> Thêm đội</button>
        </div>
    </form>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
