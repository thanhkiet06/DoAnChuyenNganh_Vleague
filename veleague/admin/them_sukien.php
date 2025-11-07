<?php
require '../auth.php';
require_role('admin');
require '../connect.php';

$id_tran = $_GET['id_tran'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_cauthu = $_POST['id_cauthu'];
    $thoigian = $_POST['thoigian'];
    $loai = $_POST['loai'];

    $stmt = $conn->prepare("INSERT INTO SU_KIEN_TRAN_DAU (ID_TRAN_DAU, ID_CAU_THU, THOI_GIAN, LOAI_SU_KIEN) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("iiis", $id_tran, $id_cauthu, $thoigian, $loai);
    $stmt->execute();

    header("Location: sukien_tran_detail.php?id_tran=$id_tran");
    exit;
}

$cauthus = $conn->query("SELECT * FROM CAU_THU");
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Thêm sự kiện trận đấu</title>
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
    </style>
</head>
<body>

<div class="container col-md-6">
    <h1 class="heading mb-4"><i class="bi bi-plus-circle me-2"></i>Thêm sự kiện cho trận đấu</h1>

    <form method="post" class="bg-white p-4 rounded shadow-sm">
        <div class="mb-3">
            <label class="form-label"><i class="bi bi-person-fill text-primary me-1"></i> Cầu thủ</label>
            <select name="id_cauthu" class="form-select" required>
                <?php while ($c = $cauthus->fetch_assoc()) { ?>
                    <option value="<?= $c['ID_CAU_THU'] ?>"><?= $c['HO_TEN'] ?></option>
                <?php } ?>
            </select>
        </div>

        <div class="mb-3">
            <label class="form-label"><i class="bi bi-clock text-warning me-1"></i> Thời gian (phút)</label>
            <input type="number" name="thoigian" class="form-control" required min="0" max="120">
        </div>

        <div class="mb-3">
            <label class="form-label"><i class="bi bi-flag-fill text-danger me-1"></i> Loại sự kiện</label>
            <select name="loai" class="form-select" required>
                <option value="Ghi bàn">⚽ Ghi bàn</option>
                <option value="Thẻ vàng">🟨 Thẻ vàng</option>
                <option value="Thẻ đỏ">🟥 Thẻ đỏ</option>
                <option value="Chấn thương">🤕 Chấn thương</option>
            </select>
        </div>

        <div class="d-flex justify-content-between">
            <a href="sukien_tran_detail.php?id_tran=<?= $id_tran ?>" class="btn btn-secondary">
                <i class="bi bi-arrow-left"></i> Quay lại
            </a>
            <button type="submit" class="btn btn-success">
                <i class="bi bi-plus-circle"></i> Thêm sự kiện
            </button>
        </div>
    </form>
</div>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
