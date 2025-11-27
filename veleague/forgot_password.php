<?php
require 'connect.php';
$error = $success = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $ten_dang_nhap = $_POST['ten_dang_nhap'];
    $new_pass = $_POST['new_pass'] ?? '';

    // Kiểm tra người dùng có tồn tại không
    $stmt = $conn->prepare("SELECT * FROM NGUOI_DUNG WHERE TEN_DANG_NHAP = ?");
    $stmt->bind_param("s", $ten_dang_nhap);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows == 0) {
        $error = "❌ Tên đăng nhập không tồn tại!";
    } elseif (!empty($new_pass)) {

        // Mã hóa mật khẩu mới trước khi cập nhật
        $hash = password_hash($new_pass, PASSWORD_DEFAULT);

        
        $update = $conn->prepare("UPDATE NGUOI_DUNG SET MAT_KHAU = ? WHERE TEN_DANG_NHAP = ?");
        $update->bind_param("ss", $hash, $ten_dang_nhap);
        $update->execute();
        $success = "✅ Mật khẩu đã được cập nhật thành công! 

            <a href='login.php'>Đăng nhập ngay</a>";
            
    } else {
        // Nếu người dùng tồn tại nhưng chưa nhập mật khẩu mới
        $show_reset = true;
    }
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Quên mật khẩu</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&family=Bebas+Neue&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background: url('https://cdn.wallpapersafari.com/15/92/SGtpz3.jpg') no-repeat center center fixed;
            background-size: cover;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }
        .reset-box {
            background-color: rgba(255,255,255,0.95);
            padding: 40px;
            border-radius: 12px;
            box-shadow: 0 8px 24px rgba(0,0,0,0.15);
            width: 100%;
            max-width: 400px;
        }
        .reset-title {
            font-family: 'Bebas Neue', cursive;
            font-size: 36px;
            margin-bottom: 20px;
            text-align: center;
            color: #2c3e50;
        }
    </style>
</head>
<body>

<div class="reset-box">
    <h1 class="reset-title">🔑 Quên mật khẩu</h1>

    <?php if ($error): ?>
        <div class="alert alert-danger"><?= $error ?></div>
    <?php elseif ($success): ?>
        <div class="alert alert-success"><?= $success ?></div>
    <?php endif; ?>

    <?php if (!isset($show_reset)): ?>
        <!-- Bước 1: Nhập tên đăng nhập -->
        <form method="POST">
            <div class="mb-3">
                <label class="form-label">Tên đăng nhập</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="bi bi-person-fill"></i></span>
                    <input type="text" name="ten_dang_nhap" class="form-control" placeholder="Nhập tên đăng nhập" required>
                </div>
            </div>
            <button type="submit" class="btn btn-primary w-100 mt-3">
                <i class="bi bi-arrow-right-circle"></i> Tiếp tục
            </button>
        </form>

    <?php else: ?>
        <!-- Bước 2: Đặt lại mật khẩu mới -->
        <form method="POST">
            <input type="hidden" name="ten_dang_nhap" value="<?= htmlspecialchars($ten_dang_nhap) ?>">

            <div class="mb-3">
                <label class="form-label">Tên đăng nhập</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="bi bi-person-fill"></i></span>
                    <input type="text" class="form-control" value="<?= htmlspecialchars($ten_dang_nhap) ?>" disabled>
                </div>
            </div>

            <div class="mb-3">
                <label class="form-label">Mật khẩu mới</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="bi bi-lock-fill"></i></span>
                    <input type="password" name="new_pass" class="form-control" placeholder="Nhập mật khẩu mới" required>
                </div>
            </div>

            <button type="submit" class="btn btn-success w-100 mt-3">
                <i class="bi bi-check-circle"></i> Đặt lại mật khẩu
            </button>
        </form>
    <?php endif; ?>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>