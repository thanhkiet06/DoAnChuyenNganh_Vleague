<?php
session_start();
require 'connect.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $ten_dang_nhap = trim($_POST['ten_dang_nhap']);
    $mat_khau = $_POST['mat_khau'];

    // 1️⃣ Truy vấn người dùng theo tên đăng nhập
    $query = "SELECT * FROM NGUOI_DUNG WHERE TEN_DANG_NHAP = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $ten_dang_nhap);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();

        // 2️⃣ Kiểm tra mật khẩu (với bcrypt)
        if (password_verify($mat_khau, $user['MAT_KHAU'])) {

            // 3️⃣ Nếu hash cũ, tự động cập nhật sang hash mới
            if (password_needs_rehash($user['MAT_KHAU'], PASSWORD_DEFAULT)) {
                $newHash = password_hash($mat_khau, PASSWORD_DEFAULT);
                $upd = $conn->prepare("UPDATE NGUOI_DUNG SET MAT_KHAU = ? WHERE ID_NGUOI_DUNG = ?");
                $upd->bind_param("si", $newHash, $user['ID_NGUOI_DUNG']);
                $upd->execute();
            }

            // 4️⃣ Lưu session
            $_SESSION['user_id'] = $user['ID_NGUOI_DUNG'];
            $_SESSION['vai_tro'] = $user['VAI_TRO'];
            $_SESSION['ten_dang_nhap'] = $user['TEN_DANG_NHAP'];

            // 5️⃣ Chuyển hướng theo vai trò
            switch ($user['VAI_TRO']) {
                case 'admin':
                    header("Location: admin/index.php");
                    break;
                case 'hlv':
                    header("Location: hlv/index.php");
                    break;
               
                default:
                    header("Location: viewer/index.php");
                    break;
            }
            exit;
        } else {
            $error = "❌ Sai tên đăng nhập hoặc mật khẩu!";
        }
    } else {
        $error = "❌ Tên đăng nhập không tồn tại!";
    }
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Đăng nhập hệ thống V.League</title>
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
        .login-box {
            background-color: rgba(255,255,255,0.95);
            padding: 40px;
            border-radius: 12px;
            box-shadow: 0 8px 24px rgba(0,0,0,0.15);
            width: 100%;
            max-width: 400px;
        }
        .login-title {
            font-family: 'Bebas Neue', cursive;
            font-size: 36px;
            margin-bottom: 20px;
            text-align: center;
            color: #2c3e50;
        }
    </style>
</head>
<body>

<div class="login-box">
    <h1 class="login-title">🏆 Đăng nhập V.League</h1>
    
    <?php if (isset($error)) : ?>
        <div class="alert alert-danger py-2"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <form method="POST">
        <div class="mb-3">
            <label class="form-label">Tên đăng nhập</label>
            <div class="input-group">
                <span class="input-group-text"><i class="bi bi-person-fill"></i></span>
                <input type="text" name="ten_dang_nhap" class="form-control" placeholder="Nhập tên đăng nhập" required>
            </div>
        </div>

        <div class="mb-3">
            <label class="form-label">Mật khẩu</label>
            <div class="input-group">
                <span class="input-group-text"><i class="bi bi-lock-fill"></i></span>
                <input type="password" name="mat_khau" class="form-control" placeholder="Nhập mật khẩu" required>
            </div>
        </div>

        <button type="submit" class="btn btn-primary w-100 mt-3">
            <i class="bi bi-box-arrow-in-right me-1"></i> Đăng nhập
        </button>

        <div class="text-center mt-3">
            <a href="forgot_password.php" class="text-decoration-none text-primary">
                <i class="bi bi-question-circle"></i> Quên mật khẩu?
            </a>
        </div>

        <div class="text-center mt-3">
            <span>Chưa có tài khoản?</span>
            <a href="register.php" class="btn btn-outline-secondary btn-sm ms-2">
                <i class="bi bi-person-plus-fill"></i> Đăng ký
            </a>
        </div>
    </form>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>