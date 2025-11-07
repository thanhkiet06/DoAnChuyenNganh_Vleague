<?php
require '../auth.php';
require_role('viewer');
require '../connect.php';

// Kiểm tra ID trận đấu
if (!isset($_GET['id'])) {
    die("Thiếu ID trận đấu.");
}

$id_tran = (int) $_GET['id'];

// Lấy thông tin trận
$tran = $conn->query("
    SELECT t.*, d1.TEN_DOI_BONG AS DOI_NHA, d2.TEN_DOI_BONG AS DOI_KHACH
    FROM TRAN_DAU t
    LEFT JOIN DOI_BONG d1 ON t.ID_DOI_1 = d1.ID_DOI_BONG
    LEFT JOIN DOI_BONG d2 ON t.ID_DOI_2 = d2.ID_DOI_BONG
    WHERE t.ID_TRAN_DAU = $id_tran
")->fetch_assoc();

if (!$tran) die("Không tìm thấy trận đấu.");

// Gửi bình luận
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $binhluan = trim($_POST['binhluan']);
    $nguoidung = $_SESSION['ten_dang_nhap'];

    if (!empty($binhluan)) {
        $stmt = $conn->prepare("INSERT INTO BINH_LUAN (ID_TRAN_DAU, TEN_DANG_NHAP, NOI_DUNG) VALUES (?, ?, ?)");
        $stmt->bind_param("iss", $id_tran, $nguoidung, $binhluan);
        $stmt->execute();
    }
}

// Lấy danh sách bình luận
$ds_bl = $conn->query("SELECT * FROM BINH_LUAN WHERE ID_TRAN_DAU = $id_tran ORDER BY ID_BINH_LUAN DESC");
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Bình luận trận đấu</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container mt-4">
    <h2 class="mb-3 text-danger">📝 Bình luận: <?= $tran['DOI_NHA'] ?> vs <?= $tran['DOI_KHACH'] ?></h2>

    <form method="POST" class="mb-4">
        <textarea name="binhluan" class="form-control" placeholder="Nhập bình luận..." required></textarea>
        <button class="btn btn-primary mt-2">Gửi</button>
    </form>

    <h5 class="mb-3">💬 Các bình luận:</h5>
    <?php while ($bl = $ds_bl->fetch_assoc()) { ?>
        <div class="bg-white p-2 mb-2 border rounded">
            <strong><?= htmlspecialchars($bl['TEN_DANG_NHAP']) ?>:</strong>
            <div><?= nl2br(htmlspecialchars($bl['NOI_DUNG'])) ?></div>
        </div>
    <?php } ?>

    <a href="trandau.php" class="btn btn-secondary mt-4">← Quay lại lịch đấu</a>
</div>

</body>
</html>
