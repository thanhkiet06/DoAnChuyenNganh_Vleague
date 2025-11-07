<?php
require '../auth.php';
require_role('admin');
require '../connect.php';

$id = $_GET['id'];
$giaidau = $conn->query("SELECT * FROM GIAI_DAU");
$doibong = $conn->query("SELECT * FROM DOI_BONG WHERE ID_DOI_BONG = $id")->fetch_assoc();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $ten = $_POST['ten'];
    $hlv = $_POST['hlv'];
    $giai = $_POST['giai'];
    $logo_path = $doibong['LOGO']; // giữ logo cũ mặc định

    // Xử lý upload logo nếu có file mới
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

    // Cập nhật DB
    $stmt = $conn->prepare("UPDATE DOI_BONG SET TEN_DOI_BONG=?, HUAN_LUYEN_VIEN=?, ID_GIAI_DAU=?, LOGO=? WHERE ID_DOI_BONG=?");
    $stmt->bind_param("ssisi", $ten, $hlv, $giai, $logo_path, $id);
    $stmt->execute();
    header("Location: doibong.php");
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Sửa đội bóng</title>
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

        .logo-preview {
            max-width: 100px;
            border-radius: 5px;
            margin-top: 5px;
        }
    </style>
</head>
<body>

<div class="container col-md-6">
    <h1 class="heading"><i class="bi bi-pencil-fill me-2"></i>Sửa thông tin đội bóng</h1>

    <form method="POST" enctype="multipart/form-data" class="bg-white p-4 rounded shadow-sm">
        <div class="mb-3">
            <label class="form-label">Tên đội</label>
            <input type="text" name="ten" class="form-control" required value="<?= $doibong['TEN_DOI_BONG'] ?>">
        </div>

        <div class="mb-3">
            <label class="form-label">Huấn luyện viên</label>
            <input type="text" name="hlv" class="form-control" required value="<?= $doibong['HUAN_LUYEN_VIEN'] ?>">
        </div>

        <div class="mb-3">
            <label class="form-label">Giải đấu</label>
            <select name="giai" class="form-select" required>
                <?php while ($g = $giaidau->fetch_assoc()) { ?>
                    <option value="<?= $g['ID_GIAI_DAU'] ?>" <?= ($g['ID_GIAI_DAU'] == $doibong['ID_GIAI_DAU']) ? 'selected' : '' ?>>
                        <?= $g['TEN_GIAI_DAU'] ?>
                    </option>
                <?php } ?>
            </select>
        </div>

        <div class="mb-3">
            <label class="form-label">Logo đội (tải ảnh mới nếu muốn thay)</label>
            <input type="file" name="logo" class="form-control" accept=".jpg,.jpeg,.png">
            <?php if (!empty($doibong['LOGO'])): ?>
                <img src="<?= $doibong['LOGO'] ?>" alt="Logo hiện tại" class="logo-preview">
            <?php endif; ?>
        </div>

        <div class="d-flex justify-content-between">
            <a href="doibong.php" class="btn btn-secondary"><i class="bi bi-arrow-left"></i> Quay lại</a>
            <button type="submit" class="btn btn-success"><i class="bi bi-check-circle"></i> Cập nhật</button>
        </div>
    </form>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
