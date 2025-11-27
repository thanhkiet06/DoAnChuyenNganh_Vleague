<?php
require 'connect.php';
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $ten = trim($_POST['ten']);
    $mk = trim($_POST['mk']);
    $email = trim($_POST['email']);
    $sdt = trim($_POST['sdt']);
    $ngay = $_POST['ngay'];

    // Kiểm tra xem tên đăng nhập hoặc email đã tồn tại chưa
    $check = $conn->prepare("SELECT * FROM NGUOI_DUNG WHERE TEN_DANG_NHAP = ? OR EMAIL = ?");
    $check->bind_param("ss", $ten, $email);
    $check->execute();
    $result = $check->get_result();

    if ($result->num_rows > 0) {
        $error = "❌ Tên đăng nhập hoặc email đã tồn tại!";
    } else {

        // Mã hóa mật khẩu
        $hash = password_hash($mk, PASSWORD_DEFAULT);


    
        $stmt = $conn->prepare("INSERT INTO NGUOI_DUNG (TEN_DANG_NHAP, MAT_KHAU, EMAIL, SDT, NGAY_SINH, VAI_TRO)
                                VALUES (?, ?, ?, ?, ?, 'viewer')");
        $stmt->bind_param("sssss", $ten, $hash, $email, $sdt, $ngay);
        $stmt->execute();

        // Chuyển hướng về trang đăng nhập
        header("Location: login.php");
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Đăng ký tài khoản V.League</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
            font-family: 'Inter', sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }
        .register-box {
            background: white;
            padding: 35px;
            border-radius: 12px;
            box-shadow: 0 8px 24px rgba(0,0,0,0.15);
            width: 100%;
            max-width: 500px;
        }
    </style>
</head>
<body>

<div class="register-box">
    <h3 class="text-center mb-4">📝 Đăng ký tài khoản</h3>

    <?php if (isset($error)): ?>
        <div class="alert alert-danger"><?= $error ?></div>
    <?php endif; ?>

    <form method="POST">
        <div class="mb-3">
            <label class="form-label">Tên đăng nhập</label>
            <input type="text" name="ten" class="form-control" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Mật khẩu</label>
            <input type="password" name="mk" class="form-control" required>
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
        <button type="submit" class="btn btn-success w-100">Tạo tài khoản</button>
    </form>
</div>

</body>
</html>