<?php
require '../auth.php';
require_role('hlv');
require '../connect.php';

$hlv = $_SESSION['ten_dang_nhap'];
$team = $conn->query("SELECT ID_DOI_BONG FROM DOI_BONG WHERE HUAN_LUYEN_VIEN = '$hlv'")->fetch_assoc();

if (!$team) {
    echo "<div class='container mt-5'>
    <p>Bạn chưa có đội bóng.</p>
    <a href='index.php' class='btn btn-secondary mt-2'>Quay lại</a>
    </div>";
    exit;
}

$id_doi = $team['ID_DOI_BONG'];

// Lấy danh sách trận đấu của đội
$trandau = $conn->query("
    SELECT t.*, d1.TEN_DOI_BONG AS DOI_NHA, d2.TEN_DOI_BONG AS DOI_KHACH, p.GHI_CHU
    FROM TRAN_DAU t
    JOIN DOI_BONG d1 ON t.ID_DOI_1 = d1.ID_DOI_BONG
    JOIN DOI_BONG d2 ON t.ID_DOI_2 = d2.ID_DOI_BONG
    LEFT JOIN PHAN_TICH_TRAN_DAU p ON t.ID_TRAN_DAU = p.ID_TRAN_DAU AND p.ID_DOI_BONG = $id_doi
    WHERE t.ID_DOI_1 = $id_doi OR t.ID_DOI_2 = $id_doi
    ORDER BY t.NGAY_THI_DAU DESC
");

// Xử lý thêm/sửa ghi chú phân tích
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['id_tran'])) {
    $id_tran = intval($_POST['id_tran']);
    $ghi_chu = trim($_POST['ghi_chu']);

    // Kiểm tra xem đã có phân tích chưa
    $existing = $conn->query("SELECT ID_PHAN_TICH FROM PHAN_TICH_TRAN_DAU WHERE ID_TRAN_DAU = $id_tran AND ID_DOI_BONG = $id_doi")->fetch_assoc();
    if ($existing) {
        $stmt = $conn->prepare("UPDATE PHAN_TICH_TRAN_DAU SET GHI_CHU = ? WHERE ID_TRAN_DAU = ? AND ID_DOI_BONG = ?");
        $stmt->bind_param("sii", $ghi_chu, $id_tran, $id_doi);
    } else {
        $stmt = $conn->prepare("INSERT INTO PHAN_TICH_TRAN_DAU (ID_TRAN_DAU, ID_DOI_BONG, GHI_CHU) VALUES (?, ?, ?)");
        $stmt->bind_param("iis", $id_tran, $id_doi, $ghi_chu);
    }
    if ($stmt->execute()) {
        $success = "Lưu phân tích thành công!";
    } else {
        $error = "Lỗi khi lưu phân tích.";
    }
}
?>

<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <title>Phân tích trận đấu - HLV</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=Inter:wght@400;600&display=swap"
        rel="stylesheet">
    <style>
    body {
        font-family: 'Inter', sans-serif;
        background-color: #f7f9fb;
    }

    .container {
        padding: 40px 20px;
        max-width: 1100px;
    }

    h2 {
        font-family: 'Inter', sans-serif;
        font-size: 42px;
        color: #d90429;
        margin-bottom: 30px;
    }

    .match-card {
        background: white;
        padding: 20px;
        border-radius: 12px;
        box-shadow: 0 6px 14px rgba(0, 0, 0, 0.08);
        margin-bottom: 20px;
    }

    .btn-save {
        background-color: #d90429;
        color: white;
    }

    .btn-save:hover {
        background-color: #b40322;
    }
    </style>
</head>

<body>
    <div class="container">
        <h2>📊 Phân tích trận đấu</h2>

        <?php if (isset($success)) { ?>
        <div class="alert alert-success"><?= $success ?></div>
        <?php } elseif (isset($error)) { ?>
        <div class="alert alert-danger"><?= $error ?></div>
        <?php } ?>

        <?php while ($row = $trandau->fetch_assoc()) { ?>
        <div class="match-card">
            <h5><?= htmlspecialchars($row['DOI_NHA']) ?> vs <?= htmlspecialchars($row['DOI_KHACH']) ?>
                (<?= $row['NGAY_THI_DAU'] ?>)</h5>
            <p><strong>Kết quả:</strong> <?= $row['KET_QUA'] ?? 'Chưa có' ?></p>
            <!-- Thống kê giả lập, có thể thay bằng truy vấn từ SU_KIEN_TRAN_DAU -->
            <p><strong>Thống kê:</strong> Bàn thắng: <?php
                $goals = $conn->query("SELECT COUNT(*) AS goals FROM SU_KIEN_TRAN_DAU WHERE ID_TRAN_DAU = {$row['ID_TRAN_DAU']} AND LOAI_SU_KIEN = 'Ghi bàn' AND ID_CAU_THU IN (SELECT ID_CAU_THU FROM CAU_THU WHERE ID_DOI_BONG = $id_doi)")->fetch_assoc();
                echo $goals['goals'];
            ?>, Sút bóng: <?php
                $shots = $conn->query("SELECT COUNT(*) AS shots FROM SU_KIEN_TRAN_DAU WHERE ID_TRAN_DAU = {$row['ID_TRAN_DAU']} AND LOAI_SU_KIEN = 'Sút bóng' AND ID_CAU_THU IN (SELECT ID_CAU_THU FROM CAU_THU WHERE ID_DOI_BONG = $id_doi)")->fetch_assoc();
                echo $shots['shots'];
            ?></p>
            <form method="POST">
                <input type="hidden" name="id_tran" value="<?= $row['ID_TRAN_DAU'] ?>">
                <div class="mb-3">
                    <label class="form-label">Ghi chú phân tích</label>
                    <textarea name="ghi_chu" class="form-control" rows="4"
                        placeholder="VD: Đội mạnh ở cánh trái, cần cải thiện phòng ngự..."><?= htmlspecialchars($row['GHI_CHU'] ?? '') ?></textarea>
                </div>
                <button type="submit" class="btn btn-save">Lưu phân tích</button>
            </form>
        </div>
        <?php } ?>

        <a href="index.php" class="btn btn-secondary mt-3">← Quay lại trang chính</a>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>