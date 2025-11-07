<?php
require '../auth.php';
require_role('admin');
require '../connect.php';

$id = $_GET['id'];
$yeucau = $conn->query("SELECT * FROM YEU_CAU_USER WHERE ID_YEU_CAU = $id")->fetch_assoc();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $trangthai = $_POST['trangthai'];

    $stmt = $conn->prepare("UPDATE YEU_CAU_USER SET TRANG_THAI=? WHERE ID_YEU_CAU=?");
    $stmt->bind_param("si", $trangthai, $id);
    $stmt->execute();
    header("Location: yeucau.php");
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Duyệt yêu cầu</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&family=Bebas+Neue&display=swap" rel="stylesheet">
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
        }
        .form-label {
            font-weight: 500;
        }
        .card {
            border-radius: 12px;
            box-shadow: 0 5px 10px rgba(0,0,0,0.05);
        }
    </style>
</head>
<body>

<div class="container col-md-7">
    <h1 class="heading mb-4"><i class="bi bi-envelope-open-fill me-2"></i>Duyệt yêu cầu người dùng</h1>

    <div class="card p-4 mb-4">
        <p><strong>📌 Loại:</strong> <?= $yeucau['LOAI_YEU_CAU'] ?></p>
        <p><strong>📝 Nội dung:</strong> <?= $yeucau['NOI_DUNG'] ?></p>
        <p><strong>📅 Ngày gửi:</strong> <?= date("d/m/Y", strtotime($yeucau['NGAY_TAO'])) ?></p>
    </div>

    <form method="POST" class="bg-white p-4 rounded shadow-sm">
        <div class="mb-3">
            <label class="form-label"><i class="bi bi-check2-circle me-1 text-success"></i> Cập nhật trạng thái</label>
            <select name="trangthai" class="form-select" required>
                <option value="Chờ duyệt" <?= $yeucau['TRANG_THAI'] == 'Chờ duyệt' ? 'selected' : '' ?>>⏳ Chờ duyệt</option>
                <option value="Đã xử lý" <?= $yeucau['TRANG_THAI'] == 'Đã xử lý' ? 'selected' : '' ?>>✅ Đã xử lý</option>
                <option value="Từ chối" <?= $yeucau['TRANG_THAI'] == 'Từ chối' ? 'selected' : '' ?>>❌ Từ chối</option>
            </select>
        </div>

        <div class="d-flex justify-content-between">
            <a href="yeucau.php" class="btn btn-secondary"><i class="bi bi-arrow-left"></i> Quay lại</a>
            <button type="submit" class="btn btn-primary"><i class="bi bi-save"></i> Cập nhật</button>
        </div>
    </form>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
