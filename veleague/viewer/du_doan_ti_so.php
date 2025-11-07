<?php
require '../auth.php';
require_role('viewer');
require '../connect.php';

// Lấy danh sách trận đấu chưa diễn ra
$today = date('Y-m-d');
$trandau = $conn->query("
    SELECT t.*, d1.TEN_DOI_BONG AS DOI_NHA, d2.TEN_DOI_BONG AS DOI_KHACH
    FROM TRAN_DAU t
    JOIN DOI_BONG d1 ON t.ID_DOI_1 = d1.ID_DOI_BONG
    JOIN DOI_BONG d2 ON t.ID_DOI_2 = d2.ID_DOI_BONG
    WHERE t.NGAY_THI_DAU >= '$today'
    ORDER BY t.NGAY_THI_DAU ASC
");

// Xử lý dự đoán
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id_tran']) && isset($_POST['ty_so'])) {
    $id_tran = intval($_POST['id_tran']);
    $ty_so = trim($_POST['ty_so']);
    $user_id = $_SESSION['user_id'];

    $stmt = $conn->prepare("INSERT INTO DU_DOAN (ID_NGUOI_DUNG, ID_TRAN_DAU, TY_SO) VALUES (?, ?, ?)");
    $stmt->bind_param("iis", $user_id, $id_tran, $ty_so);
    if ($stmt->execute()) {
        $success = "Dự đoán thành công!";
    } else {
        $error = "Lỗi khi lưu dự đoán.";
    }
}

// Lấy danh sách dự đoán của người dùng
$user_id = $_SESSION['user_id'];
$du_doan = $conn->query("SELECT ID_TRAN_DAU, TY_SO FROM DU_DOAN WHERE ID_NGUOI_DUNG = $user_id");
$du_doan_list = [];
while ($row = $du_doan->fetch_assoc()) {
    $du_doan_list[$row['ID_TRAN_DAU']] = $row['TY_SO'];
}
?>

<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <title>Dự đoán kết quả - V.League 2025</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=Inter:wght@400;600&display=swap"
        rel="stylesheet">
    <style>
    body {
        font-family: 'Inter', sans-serif;
        background-color: #f2f4f8;
    }

    .container {
        padding: 40px 20px;
    }

    .title {
        font-family: 'Bebas Neue', cursive;
        font-size: 44px;
        color: #d90429;
    }

    .match-card {
        background: white;
        padding: 15px;
        border-radius: 10px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        margin-bottom: 15px;
    }

    .btn-predict {
        background-color: #d90429;
        color: white;
    }

    .btn-predict:hover {
        background-color: #b40221;
    }

    .btn-back {
        background-color: #d90429;
        color: white;
    }

    .btn-back:hover {
        background-color: #b40221;
    }
    </style>
</head>

<body>
    <div class="container">
        <h1 class="text-center title">🔮 Dự đoán kết quả</h1>
        <p class="text-center text-muted mb-4">Dự đoán tỷ số các trận đấu sắp tới</p>

        <?php if (isset($success)) { ?>
        <div class="alert alert-success"><?= $success ?></div>
        <?php } elseif (isset($error)) { ?>
        <div class="alert alert-danger"><?= $error ?></div>
        <?php } ?>

        <?php if ($trandau->num_rows > 0) { ?>
        <div class="row g-3">
            <?php while ($row = $trandau->fetch_assoc()) { ?>
            <div class="col-12">
                <div class="match-card">
                    <h5><?= htmlspecialchars($row['DOI_NHA']) ?> vs <?= htmlspecialchars($row['DOI_KHACH']) ?></h5>
                    <p>Ngày: <?= $row['NGAY_THI_DAU'] ?> | Địa điểm: <?= htmlspecialchars($row['DIA_DIEM']) ?></p>
                    <form method="POST">
                        <input type="hidden" name="id_tran" value="<?= $row['ID_TRAN_DAU'] ?>">
                        <div class="input-group mb-2">
                            <input type="text" name="ty_so" class="form-control" placeholder="Nhập tỷ số (VD: 2-1)"
                                value="<?= isset($du_doan_list[$row['ID_TRAN_DAU']]) ? htmlspecialchars($du_doan_list[$row['ID_TRAN_DAU']]) : '' ?>"
                                required>
                            <button type="submit" class="btn btn-predict">Dự đoán</button>
                        </div>
                    </form>
                </div>
            </div>
            <?php } ?>
        </div>
        <?php } else { ?>
        <p class="text-muted text-center">Không có trận đấu nào để dự đoán.</p>
        <?php } ?>

        <div class="text-center mt-4">
            <a href="index.php" class="btn btn-back px-4">← Về trang chính</a>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>