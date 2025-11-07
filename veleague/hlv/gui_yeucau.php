<?php
require '../auth.php';
require_role('hlv');
require '../connect.php';

$id_nguoi_dung = $_SESSION['user_id'];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $loai = $_POST['loai'];
    $noidung = $_POST['noidung'];
    $ngay = date('Y-m-d');
    $trangthai = "Chờ duyệt";

    $stmt = $conn->prepare("INSERT INTO YEU_CAU_USER (LOAI_YEU_CAU, NOI_DUNG, TRANG_THAI, NGAY_TAO, ID_NGUOI_DUNG)
                            VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssi", $loai, $noidung, $trangthai, $ngay, $id_nguoi_dung);
    $stmt->execute();
    header("Location: index.php");
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Gửi yêu cầu - HLV</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    
    <!-- Bootstrap & Fonts -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=Inter:wght@400;600&display=swap" rel="stylesheet">
    
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background-color: #f7f9fb;
        }

        .container {
            max-width: 700px;
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

        textarea {
            resize: vertical;
        }
    </style>
</head>
<body>

<div class="container">
    <h2>📮 Gửi yêu cầu đến quản trị viên</h2>

    <form method="POST" class="needs-validation" novalidate>
        <div class="mb-3">
            <label class="form-label">Loại yêu cầu:</label>
            <select name="loai" class="form-select" required>
                <option value="">-- Chọn loại --</option>
                <option value="Cầu thủ">Về cầu thủ</option>
                <option value="Đội bóng">Về đội bóng</option>
                <option value="Khác">Khác</option>
            </select>
        </div>

        <div class="mb-3">
            <label class="form-label">Nội dung:</label>
            <textarea name="noidung" class="form-control" rows="5" placeholder="Nhập nội dung chi tiết..." required></textarea>
        </div>

        <button type="submit" class="btn btn-submit">Gửi yêu cầu</button>
        <a href="index.php" class="btn btn-secondary ms-2">← Quay lại</a>
    </form>
</div>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<!-- Validate form -->
<script>
    (() => {
        'use strict';
        const forms = document.querySelectorAll('.needs-validation');
        Array.from(forms).forEach(form => {
            form.addEventListener('submit', event => {
                if (!form.checkValidity()) {
                    event.preventDefault();
                    event.stopPropagation();
                }
                form.classList.add('was-validated');
            }, false);
        });
    })();
</script>
</body>
</html>
