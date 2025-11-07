<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <title>V.League 2025 - Trang chính thức</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Bootstrap & Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=Inter:wght@400;600&display=swap"
        rel="stylesheet">

    <style>
    body {
        margin: 0;
        font-family: 'Inter', sans-serif;
        background-color: #f5f7fa;
    }

    .navbar {
        background-color: #d90429;
    }

    .navbar-brand {
        font-family: 'Bebas Neue', cursive;
        font-size: 32px;
        color: #fff !important;
        letter-spacing: 2px;
    }

    .nav-link {
        color: #fff !important;
        font-weight: 500;
    }

    .hero {
        background: url('https://i.ibb.co/t3YhH4g/hero-stadium.jpg') center/cover no-repeat;
        color: white;
        padding: 80px 20px;
        text-align: center;
        position: relative;
    }

    .hero::after {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background-color: rgba(0, 0, 0, 0.5);
    }

    .hero-content {
        position: relative;
        z-index: 2;
    }

    .hero h1 {
        font-family: 'Bebas Neue', cursive;
        font-size: 64px;
        letter-spacing: 2px;
    }

    .hero p {
        font-size: 20px;
        color: #f1f1f1;
    }

    .card-custom {
        border: none;
        border-radius: 16px;
        box-shadow: 0 6px 16px rgba(0, 0, 0, 0.1);
        transition: all 0.3s;
    }

    .card-custom:hover {
        transform: translateY(-5px);
    }

    footer {
        background-color: #1e1e2f;
        color: #ccc;
        padding: 20px 0;
        text-align: center;
        font-size: 14px;
        margin-top: 60px;
    }

    .btn-veleague {
        background-color: #d90429;
        color: white;
        font-weight: 600;
    }

    .btn-veleague:hover {
        background-color: #b40221;
    }
    </style>
</head>

<body>

    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg">
        <div class="container">
            <a class="navbar-brand" href="#">⚽ V.LEAGUE 2025</a>
            <div class="collapse navbar-collapse">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a href="#" class="nav-link">
                            <i class="bi bi-house-door-fill me-1"></i>Trang
                            chủ</a>
                    </li>

                    <li class="nav-item">
                        <a href="bangxephang.php" class="nav-link">
                            <i class="bi bi-list-ol me-1"></i>Bảng
                            xếp hạng</a>
                    </li>

                    <li class="nav-item">
                        <a href="trandau.php" class="nav-link">
                            <i class="bi bi-calendar-event me-1"></i>Lịch
                            đấu</a>
                    </li>

                    <li class="nav-item">
                        <a href="doibong.php" class="nav-link">
                            <i class="bi bi-shield-fill me-1"></i>Đội
                            bóng</a>
                    </li>

                    <li class="nav-item">
                        <a href="timkiem.php" class="nav-link">
                            <i class="bi bi-search me-1"></i>Tìm
                            kiếm</a>
                    </li>


                    <?php if (isset($_SESSION['ten_dang_nhap'])): ?>
                    <li class="nav-item">
                        <a href="../logout.php" class="nav-link">
                            <i class="bi bi-person-circle"></i> <?= htmlspecialchars($_SESSION['ten_dang_nhap']) ?>
                            (Thoát)
                        </a>
                    </li>
                    <?php else: ?>
                    <li class="nav-item">
                        <a href="../login.php" class="nav-link">
                            <i class="bi bi-box-arrow-in-right"></i> Đăng nhập
                        </a>
                    </li>
                    <?php endif; ?>


                </ul>
            </div>
        </div>
    </nav>

    <!-- Hero section -->
    <section class="hero">
        <div class="hero-content">
            <h1>V.LEAGUE 2025</h1>
            <p>Theo dõi mùa giải hấp dẫn nhất Việt Nam – Kết quả, đội bóng, cầu thủ, bảng xếp hạng và hơn thế nữa!</p>
        </div>
    </section>

    <!-- Cards section -->
    <div class="container my-5">
        <div class="row g-4 text-center">
            <div class="col-md-4">
                <div class="card card-custom p-4">
                    <i class="bi bi-list-ol fs-1 text-danger"></i>
                    <h4 class="mt-3">Bảng xếp hạng</h4>
                    <p>Xem điểm số, hiệu số và thứ hạng các đội</p>
                    <a href="bangxephang.php" class="btn btn-veleague btn-sm">Xem ngay</a>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card card-custom p-4">
                    <i class="bi bi-calendar-event fs-1 text-primary"></i>
                    <h4 class="mt-3">Lịch thi đấu</h4>
                    <p>Cập nhật lịch và kết quả các trận đấu</p>
                    <a href="trandau.php" class="btn btn-veleague btn-sm">Xem lịch</a>
                    <a href="trandau.php?id=<?= $row['ID_TRAN_DAU'] ?>" class="btn btn-outline-info btn-sm">Xem chi tiết
                        & bình luận</a>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card card-custom p-4">
                    <i class="bi bi-shield-fill fs-1 text-success"></i>
                    <h4 class="mt-3">Đội bóng</h4>
                    <p>Danh sách CLB và cầu thủ thi đấu năm nay</p>
                    <a href="doibong.php" class="btn btn-veleague btn-sm">Khám phá</a>
                </div>
            </div>
        </div>
    </div>


    <!-- Footer -->
    <footer>
        &copy; 2025 V.League Portal. Designed for Hệ Thống Thông Tin 💻⚽
    </footer>

    <!-- Bootstrap Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Botsonic Embed Code -->
    <script src="https://botsonic.ai/widget.js"></script>
    <script>
    Botsonic.init({
        botId: "YOUR_BOT_ID",
        apiKey: "YOUR_API_KEY"
    });
    </script>
</body>

</html>