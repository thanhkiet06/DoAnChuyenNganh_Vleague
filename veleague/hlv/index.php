<?php
require '../auth.php';
require_role('hlv');
?>

<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <title>HLV Dashboard - V.League</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Bootstrap & Fonts -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=Inter:wght@400;600&display=swap"
        rel="stylesheet">

    <style>
    body {
        font-family: 'Inter', sans-serif;
        background-color: #f0f2f5;
        margin: 0;
    }

    .sidebar {
        position: fixed;
        top: 0;
        left: 0;
        height: 100vh;
        width: 230px;
        background-color: #1e1e2f;
        padding-top: 40px;
        color: white;
    }

    .sidebar h4 {
        font-family: 'Bebas Neue', cursive;
        font-size: 28px;
        color: #e94560;
    }

    .sidebar a {
        display: block;
        color: #ccc;
        padding: 14px 25px;
        font-size: 16px;
        text-decoration: none;
    }

    .sidebar a:hover,
    .sidebar a.active {
        background-color: #2c2c44;
        color: white;
    }

    .main-content {
        margin-left: 230px;
        padding: 50px 30px;
    }

    .heading {
        font-family: 'Bebas Neue', cursive;
        font-size: 42px;
        color: #d90429;
    }

    .subtext {
        font-size: 18px;
        color: #555;
    }

    .card {
        border-radius: 12px;
        box-shadow: 0 6px 14px rgba(0, 0, 0, 0.08);
    }

    .card h5 {
        font-size: 20px;
        font-weight: 600;
    }

    .btn-logout {
        position: absolute;
        bottom: 30px;
        left: 25px;
        width: 180px;
    }
    </style>
</head>

<body>

    <!-- Sidebar -->
    <div class="sidebar">
        <div class="text-center mb-4">
            <h4>⚽ HLV Panel</h4>
        </div>
        <a href="index.php" class="active"><i class="bi bi-house-door-fill me-2"></i>Trang chính</a>
        <a href="doibong_cuatoi.php"><i class="bi bi-people-fill me-2"></i>Đội của tôi</a>
        <a href="cauthu.php"><i class="bi bi-person-video3 me-2"></i>Quản lý cầu thủ</a>
        <a href="gui_yeucau.php"><i class="bi bi-envelope-plus me-2"></i>Gửi yêu cầu</a>
        <a href="ke_hoach_tap_luyen.php"><i class="bi bi-calendar-check-fill me-2"></i>Kế hoạch tập luyện</a>
        <a href="phan_tich_trandau.php"><i class="bi bi-bar-chart-fill me-2"></i>Phân tích trận đấu</a>
        <a href="doihinh_thidau.php"><i class="bi bi-shield-fill me-2"></i>Đội hình thi đấu</a>
        <a href="suckhoe_cauthu.php"><i class="bi bi-heart-pulse-fill me-2"></i>Sức khỏe cầu thủ</a>
        <a href="tailieu_chienthuat.php"><i class="bi bi-folder-fill me-2"></i>Tài liệu chiến thuật</a>
        <a href="hieu_suat_cau_thu.php"><i class="bi bi-graph-up me-2"></i>Hiệu suất cầu thủ</a>
        <a href="tin_nhan_noi_bo.php"><i class="bi bi-chat-dots-fill me-2"></i>Tin nhắn nội bộ</a>

        <a href="../logout.php" class="btn btn-danger btn-logout"><i class="bi bi-box-arrow-left me-2"></i>Đăng xuất</a>
    </div>

    <!-- Main content -->
    <div class="main-content">
        <div class="text-center mb-5">
            <h1 class="heading">🎮 Xin chào HLV <?= $_SESSION['ten_dang_nhap'] ?></h1>
            <p class="subtext">Hệ thống quản lý đội bóng & cầu thủ của bạn</p>
        </div>

        <div class="row g-4">
            <div class="col-md-4">
                <div class="card p-3">
                    <h5><i class="bi bi-people-fill text-primary me-2"></i>Thông tin đội của tôi</h5>
                    <p class="text-muted">Xem thông tin đội bóng do bạn quản lý</p>
                    <a href="doibong_cuatoi.php" class="btn btn-outline-primary btn-sm">Vào xem</a>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card p-3">
                    <h5><i class="bi bi-person-video3 text-success me-2"></i>Quản lý cầu thủ</h5>
                    <p class="text-muted">Cập nhật danh sách và trạng thái cầu thủ</p>
                    <a href="cauthu.php" class="btn btn-outline-success btn-sm">Vào quản lý</a>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card p-3">
                    <h5><i class="bi bi-envelope-plus text-danger me-2"></i>Gửi yêu cầu</h5>
                    <p class="text-muted">Gửi đề xuất đến ban tổ chức</p>
                    <a href="gui_yeucau.php" class="btn btn-outline-danger btn-sm">Gửi yêu cầu</a>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card p-3">
                    <h5><i class="bi bi-calendar-check-fill text-warning me-2"></i>Kế hoạch tập luyện</h5>
                    <p class="text-muted">Quản lý và xem lịch tập luyện của đội bóng</p>
                    <a href="ke_hoach_tap_luyen.php" class="btn btn-outline-warning btn-sm">Xem kế hoạch</a>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card p-3">
                    <h5><i class="bi bi-bar-chart-fill text-info me-2"></i>Phân tích trận đấu</h5>
                    <p class="text-muted">Xem và phân tích dữ liệu các trận đấu</p>
                    <a href="phan_tich_trandau.php" class="btn btn-outline-info btn-sm">Xem phân tích</a>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card p-3">
                    <h5><i class="bi bi-shield-fill text-primary me-2"></i>Đội hình thi đấu</h5>
                    <p class="text-muted">Quản lý đội hình và chiến thuật cho trận đấu</p>
                    <a href="doihinh_thidau.php" class="btn btn-outline-primary btn-sm">Quản lý đội hình</a>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card p-3">
                    <h5><i class="bi bi-folder-fill text-success me-2"></i>Tài liệu chiến thuật</h5>
                    <p class="text-muted">Quản lý và xem các tài liệu chiến thuật cho đội bóng</p>
                    <a href="tailieu_chienthuat.php" class="btn btn-outline-success btn-sm">Xem tài liệu</a>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card p-3">
                    <h5><i class="bi bi-heart-pulse-fill text-danger me-2"></i>Sức khỏe cầu thủ</h5>
                    <p class="text-muted">Theo dõi và quản lý tình trạng sức khỏe của cầu thủ</p>
                    <a href="suckhoe_cauthu.php" class="btn btn-outline-danger btn-sm">Xem chi tiết</a>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card p-3">
                    <h5><i class="bi bi-graph-up text-warning me-2"></i>Hiệu suất cầu thủ</h5>
                    <p class="text-muted">Theo dõi và phân tích hiệu suất của từng cầu thủ</p>
                    <a href="hieu_suat_cau_thu.php" class="btn btn-outline-warning btn-sm">Xem hiệu suất</a>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card p-3">
                    <h5><i class="bi bi-chat-dots-fill text-info me-2"></i>Tin nhắn nội bộ</h5>
                    <p class="text-muted">Gửi và nhận tin nhắn nội bộ giữa các thành viên</p>
                    <a href="tin_nhan_noi_bo.php" class="btn btn-outline-info btn-sm">Xem tin nhắn</a>

                    <!-- Bootstrap -->
                    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>