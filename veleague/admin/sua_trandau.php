<?php
require '../auth.php';
require_role('admin');
require '../connect.php';
require '../update_bxh.php';

$id = $_GET['id'];
$trandau = $conn->query("SELECT * FROM TRAN_DAU WHERE ID_TRAN_DAU = $id")->fetch_assoc();
$giaidau = $conn->query("SELECT * FROM GIAI_DAU");
$doibong = $conn->query("SELECT * FROM DOI_BONG");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $ngay = $_POST['ngay'];
    $diadiem = $_POST['diadiem'];
    $giai = $_POST['giai'];
    $doi1 = $_POST['doi1'];
    $doi2 = $_POST['doi2'];
    $bt1 = $_POST['bt1'];
    $bt2 = $_POST['bt2'];
    $ketqua = "$bt1 - $bt2";

    $stmt = $conn->prepare("UPDATE TRAN_DAU SET NGAY_THI_DAU=?, DIA_DIEM=?, KET_QUA=?, ID_GIAI_DAU=?, ID_DOI_1=?, ID_DOI_2=?, BAN_THANG_DOI_1=?, BAN_THANG_DOI_2=? WHERE ID_TRAN_DAU=?");
    $stmt->bind_param("sssiiiiii", $ngay, $diadiem, $ketqua, $giai, $doi1, $doi2, $bt1, $bt2, $id);
    $stmt->execute();

    update_bxh($conn, $doi1, $doi2, $bt1, $bt2);
    header("Location: trandau.php");
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Sửa trận đấu</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=Inter:wght@400;600&display=swap" rel="stylesheet">
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
            margin-bottom: 20px;
        }

        .form-label {
            font-weight: 500;
        }
    </style>
</head>
<body>

<div class="container col-md-6">
    <h1 class="heading"><i class="bi bi-pencil-square me-2"></i>Sửa thông tin trận đấu</h1>

    <form method="POST" class="bg-white p-4 rounded shadow-sm">
        <div class="mb-3">
            <label class="form-label">Ngày thi đấu</label>
            <input type="date" name="ngay" class="form-control" value="<?= $trandau['NGAY_THI_DAU'] ?>" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Địa điểm</label>
            <input type="text" name="diadiem" class="form-control" value="<?= $trandau['DIA_DIEM'] ?>" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Đội 1</label>
            <select name="doi1" class="form-select" required>
                <?php while ($d = $doibong->fetch_assoc()) { ?>
                    <option value="<?= $d['ID_DOI_BONG'] ?>" <?= ($d['ID_DOI_BONG'] == $trandau['ID_DOI_1']) ? 'selected' : '' ?>>
                        <?= $d['TEN_DOI_BONG'] ?>
                    </option>
                <?php } ?>
            </select>
        </div>

        <div class="mb-3">
            <label class="form-label">Bàn thắng đội 1</label>
            <input type="number" name="bt1" class="form-control" value="<?= $trandau['BAN_THANG_DOI_1'] ?>" min="0">
        </div>

        <div class="mb-3">
            <label class="form-label">Đội 2</label>
            <?php $doibong->data_seek(0); ?>
            <select name="doi2" class="form-select" required>
                <?php while ($d = $doibong->fetch_assoc()) { ?>
                    <option value="<?= $d['ID_DOI_BONG'] ?>" <?= ($d['ID_DOI_BONG'] == $trandau['ID_DOI_2']) ? 'selected' : '' ?>>
                        <?= $d['TEN_DOI_BONG'] ?>
                    </option>
                <?php } ?>
            </select>
        </div>

        <div class="mb-3">
            <label class="form-label">Bàn thắng đội 2</label>
            <input type="number" name="bt2" class="form-control" value="<?= $trandau['BAN_THANG_DOI_2'] ?>" min="0">
        </div>

        <div class="mb-3">
            <label class="form-label">Giải đấu</label>
            <select name="giai" class="form-select" required>
                <?php while ($g = $giaidau->fetch_assoc()) { ?>
                    <option value="<?= $g['ID_GIAI_DAU'] ?>" <?= ($g['ID_GIAI_DAU'] == $trandau['ID_GIAI_DAU']) ? 'selected' : '' ?>>
                        <?= $g['TEN_GIAI_DAU'] ?>
                    </option>
                <?php } ?>
            </select>
        </div>

        <div class="d-flex justify-content-between">
            <a href="trandau.php" class="btn btn-secondary"><i class="bi bi-arrow-left"></i> Quay lại</a>
            <button type="submit" class="btn btn-success"><i class="bi bi-check-circle"></i> Cập nhật trận đấu</button>
        </div>
    </form>
</div>

<!-- Bootstrap Bundle -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
