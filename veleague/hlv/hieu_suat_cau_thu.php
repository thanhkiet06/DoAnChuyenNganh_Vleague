<?php
require '../auth.php';
require_role('hlv');
require '../connect.php';

$hlv = $_SESSION['ten_dang_nhap'];
$team = $conn->query("SELECT ID_DOI_BONG FROM DOI_BONG WHERE HUAN_LUYEN_VIEN = '$hlv'")->fetch_assoc();

if (!$team) {
    echo "<div class='container mt-5'><p>Bạn chưa có đội bóng.</p><a href='index.php' class='btn btn-secondary mt-2'>Quay lại</a></div>";
    exit;
}

$id_doi = $team['ID_DOI_BONG'];

// Xử lý xóa hiệu suất
if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    $id_hieusuat = intval($_GET['delete']);
    $conn->query("DELETE FROM HIEU_SUAT_CAU_THU WHERE ID_HIEU_SUAT = $id_hieusuat AND ID_CAU_THU IN (SELECT ID_CAU_THU FROM CAU_THU WHERE ID_DOI_BONG = $id_doi)");
    header('Location: hieu_suat_cau_thu.php');
    exit;
}

// Lấy danh sách cầu thủ
$cauthu = $conn->query("SELECT ID_CAU_THU, HO_TEN FROM CAU_THU WHERE ID_DOI_BONG = $id_doi");

// Lấy danh sách trận đấu
$trandau = $conn->query("
    SELECT t.ID_TRAN_DAU, t.NGAY_THI_DAU, d1.TEN_DOI_BONG AS DOI_NHA, d2.TEN_DOI_BONG AS DOI_KHACH
    FROM TRAN_DAU t
    JOIN DOI_BONG d1 ON t.ID_DOI_1 = d1.ID_DOI_BONG
    JOIN DOI_BONG d2 ON t.ID_DOI_2 = d2.ID_DOI_BONG
    WHERE t.ID_DOI_1 = $id_doi OR t.ID_DOI_2 = $id_doi
    ORDER BY t.NGAY_THI_DAU DESC
");

// Lấy danh sách hiệu suất
$hieusuat = $conn->query("
    SELECT hs.*, c.HO_TEN, t.NGAY_THI_DAU, d1.TEN_DOI_BONG AS DOI_NHA, d2.TEN_DOI_BONG AS DOI_KHACH
    FROM HIEU_SUAT_CAU_THU hs
    JOIN CAU_THU c ON hs.ID_CAU_THU = c.ID_CAU_THU
    LEFT JOIN TRAN_DAU t ON hs.ID_TRAN_DAU = t.ID_TRAN_DAU
    LEFT JOIN DOI_BONG d1 ON t.ID_DOI_1 = d1.ID_DOI_BONG
    LEFT JOIN DOI_BONG d2 ON t.ID_DOI_2 = d2.ID_DOI_BONG
    WHERE c.ID_DOI_BONG = $id_doi
    ORDER BY hs.NGAY DESC
");

// Xử lý thêm hiệu suất
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['them_hieusuat'])) {
    $id_cauthu = intval($_POST['id_cauthu']);
    $id_tran = !empty($_POST['id_tran']) ? intval($_POST['id_tran']) : null;
    $ngay = $_POST['ngay'];
    $ban_thang = intval($_POST['ban_thang']);
    $kien_tao = intval($_POST['kien_tao']);
    $thoi_gian_thi_dau = intval($_POST['thoi_gian_thi_dau']);
    $danh_gia = trim($_POST['danh_gia']);

    $stmt = $conn->prepare("INSERT INTO HIEU_SUAT_CAU_THU (ID_CAU_THU, ID_TRAN_DAU, NGAY, BAN_THANG, KIEN_TAO, THOI_GIAN_THI_DAU, DANH_GIA) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("iisiiis", $id_cauthu, $id_tran, $ngay, $ban_thang, $kien_tao, $thoi_gian_thi_dau, $danh_gia);
    if ($stmt->execute()) {
        $success = "Thêm hiệu suất thành công!";
    } else {
        $error = "Lỗi khi thêm hiệu suất.";
    }
}
?>

<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <title>Hiệu suất cầu thủ - HLV</title>
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
        font-family: 'Bebas Neue', cursive;
        font-size: 40px;
        color: #d90429;
        margin-bottom: 30px;
    }

    .form-section {
        background: white;
        padding: 20px;
        border-radius: 12px;
        box-shadow: 0 6px 14px rgba(0, 0, 0, 0.08);
        margin-bottom: 30px;
    }

    .btn-them {
        background-color: #d90429;
        color: white;
    }

    .btn-them:hover {
        background-color: #b40322;
    }

    .btn-delete {
        background-color: #dc3545;
        color: white;
    }

    .btn-delete:hover {
        background-color: #b02a37;
    }

    .table thead {
        background-color: #d90429;
        color: white;
    }
    </style>
</head>

<body>
    <div class="container">
        <h2>🏆 Hiệu suất cầu thủ</h2>

        <!-- Form thêm hiệu suất -->
        <div class="form-section">
            <h4>Thêm hiệu suất mới</h4>
            <?php if (isset($success)) { ?>
            <div class="alert alert-success"><?= $success ?></div>
            <?php }

             elseif (isset($error)) { ?>
            <div class="alert alert-danger"><?= $error ?></div>
            <?php } ?>

            <form method="POST" class="needs-validation" novalidate>
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">Cầu thủ</label>
                        <select name="id_cauthu" class="form-select" required>
                            <option value="">Chọn cầu thủ</option>
                            <?php while ($row = $cauthu->fetch_assoc()) { ?>
                            <option value="<?= $row['ID_CAU_THU'] ?>"><?= htmlspecialchars($row['HO_TEN']) ?></option>
                            <?php } ?>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Trận đấu (tùy chọn)</label>
                        <select name="id_tran" class="form-select">
                            <option value="">Không liên kết</option>
                            <?php while ($row = $trandau->fetch_assoc()) { ?>
                            <option value="<?= $row['ID_TRAN_DAU'] ?>">
                                <?= htmlspecialchars($row['DOI_NHA']) ?> vs <?= htmlspecialchars($row['DOI_KHACH']) ?>
                                (<?= $row['NGAY_THI_DAU'] ?>)
                            </option>
                            <?php } ?>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Ngày</label>
                        <input type="date" name="ngay" class="form-control" required>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Bàn thắng</label>
                        <input type="number" name="ban_thang" class="form-control" value="0" min="0">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Kiến tạo</label>
                        <input type="number" name="kien_tao" class="form-control" value="0" min="0">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Thời gian thi đấu (phút)</label>
                        <input type="number" name="thoi_gian_thi_dau" class="form-control" value="0" min="0">
                    </div>
                    <div class="col-12">
                        <label class="form-label">Đánh giá</label>
                        <textarea name="danh_gia" class="form-control" rows="3"
                            placeholder="VD: Chơi tốt ở hiệp 1, cần cải thiện chuyền bóng..."></textarea>
                    </div>
                    <div class="col-12">
                        <button type="submit" name="them_hieusuat" class="btn btn-them">Thêm hiệu suất</button>
                    </div>
                </div>
            </form>
        </div>

        <!-- Danh sách hiệu suất -->
        <h4>Lịch sử hiệu suất</h4>
        <div class="table-responsive">
            <table class="table table-bordered align-middle">
                <thead>
                    <tr>
                        <th>Cầu thủ</th>
                        <th>Trận đấu</th>
                        <th>Ngày</th>
                        <th>Bàn thắng</th>
                        <th>Kiến tạo</th>
                        <th>Thời gian</th>
                        <th>Đánh giá</th>
                        <th>Hành động</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $hieusuat->fetch_assoc()) { ?>
                    <tr>
                        <td><?= htmlspecialchars($row['HO_TEN']) ?></td>
                        <td>
                            <?php if ($row['ID_TRAN_DAU']) { ?>
                            <?= htmlspecialchars($row['DOI_NHA']) ?> vs <?= htmlspecialchars($row['DOI_KHACH']) ?>
                            (<?= $row['NGAY_THI_DAU'] ?>)
                            <?php } else { ?>
                            Không liên kết
                            <?php } ?>
                        </td>
                        <td><?= $row['NGAY'] ?></td>
                        <td><?= $row['BAN_THANG'] ?></td>
                        <td><?= $row['KIEN_TAO'] ?></td>
                        <td><?= $row['THOI_GIAN_THI_DAU'] ?> phút</td>
                        <td><?= htmlspecialchars($row['DANH_GIA'] ?? '') ?></td>
                        <td>
                            <a href="hieu_suat_cau_thu.php?delete=<?= $row['ID_HIEU_SUAT'] ?>"
                                class="btn btn-delete btn-sm" onclick="return confirm('Xóa hiệu suất này?')">Xóa</a>
                        </td>
                    </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>

        <a href="index.php" class="btn btn-secondary mt-3">← Quay lại trang chính</a>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
    (() => {
        'use strict';
        const forms = document.querySelectorAll('.needs-validation');
        Array.from(forms).forEach(form => {
            form.addEventListener('submit', event => {
                if (!form.checkValidity()) {
                    event.preventDefault();
                    event.stopPropagation();
                }
                form.classList.add('was-validated');
            }, false);
        });
    })();
    </script>
</body>

</html>