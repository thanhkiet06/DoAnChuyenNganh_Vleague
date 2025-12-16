<?php
require '../auth.php';
require_role('viewer');
require '../connect.php';

// Lấy danh sách đội bóng
$doibong = $conn->query("SELECT ID_DOI_BONG, TEN_DOI_BONG FROM DOI_BONG ORDER BY TEN_DOI_BONG");

$doi1 = $doi2 = null;
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['doi1']) && isset($_POST['doi2'])) {
    $id_doi1 = intval($_POST['doi1']);
    $id_doi2 = intval($_POST['doi2']);

    // Lấy thông tin đội bóng
    $doi1 = $conn->query("
        SELECT d.TEN_DOI_BONG, bxh.*
        FROM DOI_BONG d
         JOIN BANG_XEP_HANG bxh ON d.ID_DOI_BONG = bxh.ID_DOI_BONG
        WHERE d.ID_DOI_BONG = $id_doi1
    ")->fetch_assoc();

    $doi2 = $conn->query("
        SELECT d.TEN_DOI_BONG, bxh.*
        FROM DOI_BONG d
         JOIN BANG_XEP_HANG bxh ON d.ID_DOI_BONG = bxh.ID_DOI_BONG
        WHERE d.ID_DOI_BONG = $id_doi2
    ")->fetch_assoc();

    // Lấy lịch sử đối đầu
    $lichsu = $conn->query("
        SELECT t.*, d1.TEN_DOI_BONG AS DOI_NHA, d2.TEN_DOI_BONG AS DOI_KHACH
        FROM TRAN_DAU t
        JOIN DOI_BONG d1 ON t.ID_DOI_1 = d1.ID_DOI_BONG
        JOIN DOI_BONG d2 ON t.ID_DOI_2 = d2.ID_DOI_BONG
        WHERE (t.ID_DOI_1 = $id_doi1 AND t.ID_DOI_2 = $id_doi2) OR (t.ID_DOI_1 = $id_doi2 AND t.ID_DOI_2 = $id_doi1)
        ORDER BY t.NGAY_THI_DAU DESC
    ");
}
?>

<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <title>So sánh đội bóng - V.League 2025</title>
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

    .vs {
        font-size: 24px;
        font-weight: bold;
        color: #d90429;
    }

    .team-stats {
        background: white;
        padding: 20px;
        border-radius: 10px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
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
        <h1 class="text-center title">⚔️ So sánh đội bóng</h1>
        <p class="text-center text-muted mb-4">Chọn hai đội để so sánh chi tiết</p>

        <form method="POST" class="row g-3 mb-4">
            <div class="col-md-5">
                <select name="doi1" class="form-select" required>
                    <option value="">Chọn đội 1</option>
                    <?php while ($row = $doibong->fetch_assoc()) { ?>
                    <option value="<?= $row['ID_DOI_BONG'] ?>"><?= htmlspecialchars($row['TEN_DOI_BONG']) ?></option>
                    <?php } $doibong->data_seek(0); ?>
                </select>
            </div>
            <div class="col-md-2 text-center vs">VS</div>
            <div class="col-md-5">
                <select name="doi2" class="form-select" required>
                    <option value="">Chọn đội 2</option>
                    <?php while ($row = $doibong->fetch_assoc()) { ?>
                    <option value="<?= $row['ID_DOI_BONG'] ?>"><?= htmlspecialchars($row['TEN_DOI_BONG']) ?></option>
                    <?php } ?>
                </select>
            </div>
            <div class="col-12 text-center">
                <button type="submit" class="btn btn-primary">So sánh</button>
            </div>
        </form>

        <?php if (!$doi1 || !$doi2) {
            echo "<p class='text-danger'>Vui lòng chọn cả hai đội để so sánh.</p>";
        } ?>

        <?php if ($doi1 && $doi2) { ?>
        <div class="row g-3">
            <div class="col-md-6">
                <div class="team-stats">
                    <h4><?= htmlspecialchars($doi1['TEN_DOI_BONG']) ?></h4>
                    <p>Điểm: <?= $doi1['DIEM_SO'] ?? 0 ?></p>
                    <p>Trận: <?= $doi1['SO_TRAN'] ?? 0 ?></p>
                    <p>Thắng: <?= $doi1['SO_THANG'] ?? 0 ?></p>
                    <p>Hòa: <?= $doi1['SO_HOA'] ?? 0 ?></p>
                    <p>Thua: <?= $doi1['SO_THA'] ?? 0 ?></p>
                    <p>Bàn thắng: <?= $doi1['BAN_THANG'] ?? 0 ?></p>
                    <p>Bàn thua: <?= $doi1['BAN_THA'] ?? 0 ?></p>
                    <p>Hiệu số: <?= $doi1['HIEU_SO'] ?? 0 ?></p>
                </div>
            </div>
            <div class="col-md-6">
                <div class="team-stats">
                    <h4><?= htmlspecialchars($doi2['TEN_DOI_BONG']) ?></h4>
                    <p>Điểm: <?= $doi2['DIEM_SO'] ?? 0 ?></p>
                    <p>Trận: <?= $doi2['SO_TRAN'] ?? 0 ?></p>
                    <p>Thắng: <?= $doi2['SO_THANG'] ?? 0 ?></p>
                    <p>Hòa: <?= $doi2['SO_HOA'] ?? 0 ?></p>
                    <p>Thua: <?= $doi2['SO_THA'] ?? 0 ?></p>
                    <p>Bàn thắng: <?= $doi2['BAN_THANG'] ?? 0 ?></p>
                    <p>Bàn thua: <?= $doi2['BAN_THA'] ?? 0 ?></p>
                    <p>Hiệu số: <?= $doi2['HIEU_SO'] ?? 0 ?></p>
                </div>
            </div>
        </div>

        <h4 class="mt-4">📜 Lịch sử đối đầu</h4>
        <?php if ($lichsu->num_rows > 0) { ?>
        <div class="table-responsive">
            <table class="table table-bordered table-striped">
                <thead class="table-danger">
                    <tr>
                        <th>Ngày</th>
                        <th>Trận đấu</th>
                        <th>Kết quả</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $lichsu->fetch_assoc()) { ?>
                    <tr>
                        <td><?= $row['NGAY_THI_DAU'] ?></td>
                        <td><?= htmlspecialchars($row['DOI_NHA']) ?> vs <?= htmlspecialchars($row['DOI_KHACH']) ?></td>
                        <td><?= $row['KET_QUA'] ?></td>
                    </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
        <?php } else { ?>
        <p class="text-muted">Chưa có trận đối đầu nào giữa hai đội.</p>
        <?php } ?>
        <?php } ?>

        <div class="text-center mt-4">
            <a href="index.php" class="btn btn-back px-4">← Về trang chính</a>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>