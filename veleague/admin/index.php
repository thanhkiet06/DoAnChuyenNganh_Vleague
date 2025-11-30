<?php
require '../auth.php';
require_role('admin');
require '../connect.php';

// Thống kê nhanh
$so_doi = $conn->query("SELECT COUNT(*) FROM DOI_BONG")->fetch_row()[0];
$so_tran = $conn->query("SELECT COUNT(*) FROM TRAN_DAU")->fetch_row()[0];
$so_yeucau = $conn->query("SELECT COUNT(*) FROM YEU_CAU_USER WHERE TRANG_THAI = 'chưa duyệt'")->fetch_row()[0];
?>
<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <title>Dashboard Admin - V.League</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- Bootstrap + Icons + Fonts -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=Inter:wght@400;600&display=swap"
        rel="stylesheet">
    <style>
    body {
        font-family: 'Inter', sans-serif;
        margin: 0;
        background: url('https://img.freepik.com/free-photo/soccer-field_53876-14357.jpg') no-repeat center center fixed;
        background-size: cover;
    }

    .sidebar {
        position: fixed;
        top: 0;
        left: 0;
        height: 100vh;
        width: 200px;
        background-color: rgba(30, 30, 47, 0.95);
        padding-top: 30px;
        color: white;
    }

    .sidebar h4 {
        font-family: 'Bebas Neue', sans-serif;
        font-size: 26px;
        text-align: center;
        color: #e94560;
    }

    .sidebar a {
        display: block;
        color: #ccc;
        padding: 12px 20px;
        font-size: 15px;
        text-decoration: none;
    }

    .sidebar a:hover,
    .sidebar a.active {
        background-color: #292943;
        color: #fff;
    }

    .main-content {
        margin-left: 200px;
        padding: 40px;
        background-color: rgba(255, 255, 255, 0.92);
        min-height: 100vh;
    }

    .heading {
        font-family: 'Bebas Neue', sans-serif;
        font-size: 40px;            /* nhỏ hơn, vừa mắt hơn */
        font-weight: 600;
        color: #e94560;
        letter-spacing: 1px;        /* chữ thoáng hơn */
    }


    .subtext {
        font-size: 18px;
        color: #333;
    }

    .stat-card {
        border-radius: 15px;
        background-color: #ffffff;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        padding: 25px;
    }

    .stat-icon {
        font-size: 30px;
        margin-bottom: 10px;
    }

    .quick-link .card {
        transition: transform 0.2s;
    }

    .quick-link .card:hover {
        transform: scale(1.03);
    }

    .btn-logout {
        position: absolute;
        bottom: 30px;
        left: 20px;
        width: 160px;
    }
    </style>
</head>

<body>

    <!-- Sidebar -->
    <div class="sidebar">
        <h4>⚽ V.League</h4>
        <a href="index.php" class="active"><i class="bi bi-house-door-fill me-2"></i>Trang chính</a>
        <a href="nguoidung.php"><i class="bi bi-person-lines-fill me-2"></i>Người dùng</a>
        <a href="doibong.php"><i class="bi bi-shield-fill me-2"></i>Đội bóng</a>
        <a href="giaidau.php"><i class="bi bi-award-fill me-2"></i>Giải đấu</a>
        <a href="trandau.php"><i class="bi bi-calendar-event-fill me-2"></i>Trận đấu</a>
        <a href="sukien_tran.php"><i class="bi bi-flag-fill me-2"></i>Sự kiện</a>
        <a href="yeucau.php"><i class="bi bi-envelope-open-fill me-2"></i>Yêu cầu</a>
        <a href="../logout.php" class="btn btn-danger btn-logout"><i class="bi bi-box-arrow-left me-2"></i>Đăng xuất</a>
        <a href="binhluan.php"><i class="bi bi-chat-dots-fill me-2"></i>Bình luận</a>
        <a href="thongke_binhchon.php"><i class="bi bi-bar-chart-fill me-2"></i>Thống kê bình chọn</a>
        <a href="highlight.php"><i class="bi bi-camera-video-fill me-2"></i>Highlight Trận Đấu</a>
    </div>

    <!-- Main content -->
    <div class="main-content">
        <div class="mb-4">
            <h1 class="heading">QUẢN TRỊ HỆ THỐNG V.LEAGUE</h1>
            <p class="subtext">Xin chào, <strong><?= $_SESSION['ten_dang_nhap'] ?></strong></p>
        </div>

        <!-- Thống kê nhanh -->
        <div class="row g-4 mb-5">
            <div class="col-md-4">
                <div class="stat-card text-center">
                    <div class="stat-icon text-primary"><i class="bi bi-shield-fill"></i></div>
                    <h5>Tổng số đội bóng</h5>
                    <h3 class="text-dark"><?= $so_doi ?></h3>
                </div>
            </div>
            <div class="col-md-4">
                <div class="stat-card text-center">
                    <div class="stat-icon text-success"><i class="bi bi-calendar-event-fill"></i></div>
                    <h5>Tổng số trận đấu</h5>
                    <h3 class="text-dark"><?= $so_tran ?></h3>
                </div>
            </div>
            <div class="col-md-4">
                <div class="stat-card text-center">
                    <div class="stat-icon text-danger"><i class="bi bi-envelope-exclamation-fill"></i></div>
                    <h5>Yêu cầu chưa duyệt</h5>
                    <h3 class="text-dark"><?= $so_yeucau ?></h3>
                </div>
            </div>
        </div>

        <!-- Liên kết nhanh -->
        <div class="row g-4 quick-link">
            <div class="col-md-4">
                <div class="card p-3">
                    <h5><i class="bi bi-person-lines-fill text-primary me-2"></i>Người dùng</h5>
                    <p class="text-muted">Admin, HLV, người xem</p>
                    <a href="nguoidung.php" class="btn btn-outline-primary btn-sm">Quản lý</a>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card p-3">
                    <h5><i class="bi bi-shield-fill text-success me-2"></i>Đội bóng</h5>
                    <p class="text-muted">Các CLB tham dự</p>
                    <a href="doibong.php" class="btn btn-outline-success btn-sm">Quản lý</a>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card p-3">
                    <h5><i class="bi bi-calendar-event-fill text-warning me-2"></i>Trận đấu</h5>
                    <p class="text-muted">Lịch + kết quả</p>
                    <a href="trandau.php" class="btn btn-outline-warning btn-sm">Quản lý</a>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card p-3">
                    <h5><i class="bi bi-award-fill text-info me-2"></i>Giải đấu</h5>
                    <p class="text-muted">Mùa giải & thông tin</p>
                    <a href="giaidau.php" class="btn btn-outline-info btn-sm">Quản lý</a>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card p-3">
                    <h5><i class="bi bi-flag-fill text-danger me-2"></i>Sự kiện trận đấu</h5>
                    <p class="text-muted">Thẻ, bàn thắng...</p>
                    <a href="sukien_tran.php" class="btn btn-outline-danger btn-sm">Quản lý</a>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card p-3">
                    <h5><i class="bi bi-envelope-open-fill text-secondary me-2"></i>Yêu cầu người dùng</h5>
                    <p class="text-muted">Duyệt từ HLV</p>
                    <a href="yeucau.php" class="btn btn-outline-secondary btn-sm">Xử lý</a>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card p-3">
                    <h5><i class="bi bi-chat-dots-fill text-dark me-2"></i>Bình luận trận đấu</h5>
                    <p class="text-muted">Xem và xoá bình luận</p>
                    <a href="binhluan.php" class="btn btn-outline-dark btn-sm">Quản lý</a>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card p-3">
                    <h5><i class="bi bi-bar-chart-fill text-primary me-2"></i>Thống kê bình chọn</h5>
                    <p class="text-muted">Xem kết quả bình chọn cầu thủ</p>
                    <a href="thongke_binhchon.php" class="btn btn-outline-primary btn-sm">Xem thống kê</a>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card p-3">
                    <h5><i class="bi bi-camera-video-fill text-danger me-2"></i>Highlight Trận Đấu</h5>
                    <p class="text-muted">Quản Lý Video Highlight</p>
                    <a href="highlight.php" class="btn btn-outline-danger btn-sm">Quản lý</a>
                </div>
            </div>
        </div>
    </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>