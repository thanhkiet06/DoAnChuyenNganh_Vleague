<?php
require '../auth.php';
require_role('hlv');
require '../connect.php';

$hlv = $_SESSION['ten_dang_nhap'];
$team = $conn->query("SELECT ID_DOI_BONG FROM DOI_BONG WHERE HUAN_LUYEN_VIEN = '$hlv'")->fetch_assoc();
$id_doi = $team['ID_DOI_BONG'];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $ten = $_POST['ten'];
    $ngay = $_POST['ngay'];
    $vitri = $_POST['vitri'];
    $soao = $_POST['soao'];
    $trangthai = $_POST['trangthai'];

    // Xử lý ảnh upload
    $anh = null;
    if (isset($_FILES['anh']) && $_FILES['anh']['error'] == 0) {
        $target_dir = "../uploads/cauthu/";
        if (!is_dir($target_dir)) mkdir($target_dir, 0777, true);
        $filename = uniqid() . "_" . basename($_FILES["anh"]["name"]);
        $target_file = $target_dir . $filename;
        if (move_uploaded_file($_FILES["anh"]["tmp_name"], $target_file)) {
            $anh = "uploads/cauthu/" . $filename;
        }
    }

    $stmt = $conn->prepare("INSERT INTO CAU_THU (HO_TEN, NGAY_SINH, VI_TRI, SO_AO, TRANG_THAI, ANH_DAI_DIEN, ID_DOI_BONG)
                            VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("sssissi", $ten, $ngay, $vitri, $soao, $trangthai, $anh, $id_doi);
    $stmt->execute();
    header("Location: cauthu.php");
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Thêm cầu thủ - HLV</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=Inter:wght@400;600&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background-color: #f7f9fa;
        }

        .container {
            max-width: 650px;
            padding: 50px 20px;
        }

        h2 {
            font-family: 'Bebas Neue', cursive;
            font-size: 36px;
            color: #d90429;
            margin-bottom: 30px;
        }

        .form-label {
            font-weight: 600;
        }

        .btn-submit {
            background-color: #d90429;
            color: white;
        }

        .btn-submit:hover {
            background-color: #b40322;
        }
    </style>
</head>
<body>

<div class="container">
    <h2>➕ Thêm cầu thủ mới</h2>

    <form method="POST" enctype="multipart/form-data" class="needs-validation" novalidate>
        <div class="mb-3">
            <label class="form-label">Họ tên:</label>
            <input type="text" name="ten" class="form-control" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Ngày sinh:</label>
            <input type="date" name="ngay" class="form-control" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Vị trí:</label>
            <input type="text" name="vitri" class="form-control" placeholder="Tiền đạo, Hậu vệ..." required>
        </div>

        <div class="mb-3">
            <label class="form-label">Số áo:</label>
            <input type="number" name="soao" class="form-control" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Trạng thái:</label>
            <input type="text" name="trangthai" class="form-control" value="Bình thường">
        </div>

        <div class="mb-3">
            <label class="form-label">Ảnh đại diện:</label>
            <input type="file" name="anh" class="form-control" accept="image/*">
        </div>

        <button type="submit" class="btn btn-submit">Thêm cầu thủ</button>
        <a href="cauthu.php" class="btn btn-secondary ms-2">← Quay lại</a>
    </form>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
    (() => {
        'use strict'
        const forms = document.querySelectorAll('.needs-validation')
        Array.from(forms).forEach(form => {
            form.addEventListener('submit', event => {
                if (!form.checkValidity()) {
                    event.preventDefault()
                    event.stopPropagation()
                }
                form.classList.add('was-validated')
            }, false)
        })
    })()
</script>
</body>
</html>
