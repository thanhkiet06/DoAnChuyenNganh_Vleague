<?php
require '../auth.php';
require_role('viewer');
require '../connect.php';

// Lấy ID trận đấu từ URL
if (!isset($_GET['id']) || empty($_GET['id'])) {
    die('Thiếu ID trận đấu.');
}

$id_tran = intval($_GET['id']);

// Kiểm tra trận đấu tồn tại
$tran = $conn->query("
    SELECT t.*, d1.TEN_DOI_BONG AS DOI1, d2.TEN_DOI_BONG AS DOI2
    FROM TRAN_DAU t
    JOIN DOI_BONG d1 ON t.ID_DOI_1 = d1.ID_DOI_BONG
    JOIN DOI_BONG d2 ON t.ID_DOI_2 = d2.ID_DOI_BONG
    WHERE t.ID_TRAN_DAU = $id_tran
")->fetch_assoc();

if (!$tran) {
    die('Không tìm thấy trận đấu.');
}

// Lấy danh sách cầu thủ 2 đội
$cauthu = $conn->query("
    SELECT c.*
    FROM CAU_THU c
    WHERE c.ID_DOI_BONG IN ($tran[ID_DOI_1], $tran[ID_DOI_2])
");

// Xử lý khi người dùng bình chọn
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id_cauthu'])) {
    $id_cauthu = intval($_POST['id_cauthu']);
    $stmt = $conn->prepare("INSERT INTO BINH_CHON_CAU_THU (ID_TRAN_DAU, ID_CAU_THU) VALUES (?, ?)");
    $stmt->bind_param("ii", $id_tran, $id_cauthu);
    if ($stmt->execute()) {
        $success = "Bình chọn thành công!";
    } else {
        $error = "Lỗi khi bình chọn.";
    }
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Bình chọn cầu thủ trận <?= htmlspecialchars($tran['DOI1']) ?> vs <?= htmlspecialchars($tran['DOI2']) ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; background-color: #f9fafc; }
        .container { max-width: 800px; padding: 40px 20px; }
        .title { font-size: 30px; font-weight: 600; color: #d90429; margin-bottom: 30px; text-align: center; }
        .card { border-radius: 10px; }
        .btn-vote { background-color: #d90429; color: white; }
        .btn-vote:hover { background-color: #b40221; }
    </style>
</head>
<body>

<div class="container">
    <h1 class="title">🏆 Bình chọn cầu thủ xuất sắc</h1>
    <h5 class="text-center mb-4"><?= htmlspecialchars($tran['DOI1']) ?> vs <?= htmlspecialchars($tran['DOI2']) ?> (<?= htmlspecialchars($tran['NGAY_THI_DAU']) ?>)</h5>

    <?php if (isset($success)) : ?>
        <div class="alert alert-success"><?= $success ?></div>
    <?php elseif (isset($error)) : ?>
        <div class="alert alert-danger"><?= $error ?></div>
    <?php endif; ?>

    <div class="row g-3">
        <?php while ($c = $cauthu->fetch_assoc()): ?>
            <div class="col-md-6">
                <div class="card p-3 shadow-sm">
                    <h5><?= htmlspecialchars($c['HO_TEN']) ?> (#<?= $c['SO_AO'] ?>)</h5>
                    <p class="mb-2"><small><?= htmlspecialchars($c['VI_TRI']) ?> | <?= htmlspecialchars($c['TRANG_THAI']) ?></small></p>
                    <form method="POST">
                        <input type="hidden" name="id_cauthu" value="<?= $c['ID_CAU_THU'] ?>">
                        <button type="submit" class="btn btn-vote w-100">Bình chọn</button>
                    </form>
                </div>
            </div>
        <?php endwhile; ?>
    </div>

    <div class="text-center mt-4">
        <a href="trandau.php" class="btn btn-secondary">← Quay lại Lịch đấu</a>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
