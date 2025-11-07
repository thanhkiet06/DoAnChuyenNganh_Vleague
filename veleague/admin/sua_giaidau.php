<?php
require '../auth.php';
require_role('admin');
require '../connect.php';

$id = $_GET['id'];
$giai = $conn->query("SELECT * FROM GIAI_DAU WHERE ID_GIAI_DAU = $id")->fetch_assoc();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $ten = $_POST['ten'];
    $bd = $_POST['bd'];
    $kt = $_POST['kt'];
    $dd = $_POST['diadiem'];

    $stmt = $conn->prepare("UPDATE GIAI_DAU SET TEN_GIAI_DAU=?, NGAY_BAT_DAU=?, NGAY_KET_THUC=?, DIA_DIEM=? WHERE ID_GIAI_DAU=?");
    $stmt->bind_param("ssssi", $ten, $bd, $kt, $dd, $id);
    $stmt->execute();
    header("Location: giaidau.php");
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Sửa giải đấu</title>
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
            font-size: 36px;
            color: #2c3e50;
        }
        .form-label {
            font-weight: 500;
        }
    </style>
</head>
<body>

<div class="container col-md-6">
    <h1 class="heading mb-4"><i class="bi bi-pencil-square me-2"></i>Sửa giải đấu</h1>

    <form method="POST" class="bg-white p-4 rounded shadow-sm">
        <div class="mb-3">
            <label class="form-label">Tên giải</label>
            <input type="text" name="ten" class="form-control" required value="<?= $giai['TEN_GIAI_DAU'] ?>">
        </div>

        <div class="mb-3">
            <label class="form-label">Ngày bắt đầu</label>
            <input type="date" name="bd" class="form-control" required value="<?= $giai['NGAY_BAT_DAU'] ?>">
        </div>

        <div class="mb-3">
            <label class="form-label">Ngày kết thúc</label>
            <input type="date" name="kt" class="form-control" required value="<?= $giai['NGAY_KET_THUC'] ?>">
        </div>

        <div class="mb-3">
            <label class="form-label">Địa điểm</label>
            <input type="text" name="diadiem" class="form-control" required value="<?= $giai['DIA_DIEM'] ?>">
        </div>

        <div class="d-flex justify-content-between">
            <a href="giaidau.php" class="btn btn-secondary"><i class="bi bi-arrow-left"></i> Quay lại</a>
            <button type="submit" class="btn btn-success"><i class="bi bi-check-circle"></i> Cập nhật</button>
        </div>
    </form>
</div>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
