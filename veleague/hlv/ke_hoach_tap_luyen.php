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

// Lấy danh sách cầu thủ
$cauthu = $conn->query("SELECT ID_CAU_THU, HO_TEN FROM CAU_THU WHERE ID_DOI_BONG = $id_doi");

// Lấy danh sách kế hoạch tập luyện
$kehoach = $conn->query("SELECT * FROM KE_HOACH_TAP_LUYEN WHERE ID_DOI_BONG = $id_doi ORDER BY NGAY_TAP DESC");

// Xử lý thêm kế hoạch
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['them_kehoach'])) {
    $ngay_tap = $_POST['ngay_tap'];
    $gio_tap = $_POST['gio_tap'];
    $noi_dung = $_POST['noi_dung'];
    $danh_sach_cauthu = isset($_POST['cauthu']) ? implode(',', $_POST['cauthu']) : '';

    $stmt = $conn->prepare("INSERT INTO KE_HOACH_TAP_LUYEN (ID_DOI_BONG, NGAY_TAP, GIO_TAP, NOI_DUNG, DANH_SACH_CAU_THU) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("issss", $id_doi, $ngay_tap, $gio_tap, $noi_dung, $danh_sach_cauthu);
    if ($stmt->execute()) {
        $success = "Thêm kế hoạch tập luyện thành công!";
    } else {
        $error = "Lỗi khi thêm kế hoạch.";
    }
}

// Xử lý xóa kế hoạch
if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    $id_kehoach = intval($_GET['delete']);
    $conn->query("DELETE FROM KE_HOACH_TAP_LUYEN WHERE ID_KE_HOACH = $id_kehoach AND ID_DOI_BONG = $id_doi");
    header('Location: ke_hoach_tap_luyen.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <title>Kế hoạch tập luyện - HLV</title>
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
        <h2>📅 Kế hoạch tập luyện</h2>

        <!-- Form thêm kế hoạch -->
        <div class="form-section">
            <h4>Thêm kế hoạch tập luyện mới</h4>
            <?php if (isset($success)) { ?>
            <div class="alert alert-success"><?= $success ?></div>
            <?php } elseif (isset($error)) { ?>
            <div class="alert alert-danger"><?= $error ?></div>
            <?php } ?>
            <form method="POST" class="needs-validation" novalidate>
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">Ngày tập</label>
                        <input type="date" name="ngay_tap" class="form-control" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Giờ tập</label>
                        <input type="time" name="gio_tap" class="form-control" required>
                    </div>
                    <div class="col-12">
                        <label class="form-label">Nội dung tập luyện</label>
                        <textarea name="noi_dung" class="form-control" rows="4"
                            placeholder="VD: Tập chiến thuật phòng ngự, rèn thể lực..." required></textarea>
                    </div>
                    <div class="col-12">
                        <label class="form-label">Cầu thủ tham gia</label>
                        <select name="cauthu[]" class="form-select" multiple>
                            <?php while ($row = $cauthu->fetch_assoc()) { ?>
                            <option value="<?= $row['ID_CAU_THU'] ?>"><?= htmlspecialchars($row['HO_TEN']) ?></option>
                            <?php } ?>
                        </select>
                        <small class="form-text text-muted">Giữ Ctrl để chọn nhiều cầu thủ.</small>
                    </div>
                    <div class="col-12">
                        <button type="submit" name="them_kehoach" class="btn btn-them">Thêm kế hoạch</button>
                    </div>
                </div>
            </form>
        </div>

        <!-- Danh sách kế hoạch -->
        <h4>Danh sách kế hoạch tập luyện</h4>
        <div class="table-responsive">
            <table class="table table-bordered align-middle text-center">
                <thead>
                    <tr>
                        <th>Ngày tập</th>
                        <th>Giờ tập</th>
                        <th>Nội dung</th>
                        <th>Cầu thủ tham gia</th>
                        <th>Hành động</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $kehoach->fetch_assoc()) { ?>
                    <tr>
                        <td><?= $row['NGAY_TAP'] ?></td>
                        <td><?= $row['GIO_TAP'] ?></td>
                        <td><?= htmlspecialchars($row['NOI_DUNG']) ?></td>
                        <td>
                            <?php
                            if ($row['DANH_SACH_CAU_THU']) {
                                $ids = explode(',', $row['DANH_SACH_CAU_THU']);
                                $names = $conn->query("SELECT HO_TEN FROM CAU_THU WHERE ID_CAU_THU IN (" . implode(',', array_map('intval', $ids)) . ")");
                                $cauthu_names = [];
                                while ($name = $names->fetch_assoc()) {
                                    $cauthu_names[] = $name['HO_TEN'];
                                }
                                echo htmlspecialchars(implode(', ', $cauthu_names));
                            } else {
                                echo 'Tất cả';
                            }
                            ?>
                        </td>
                        <td>
                            <a href="ke_hoach_tap_luyen.php?delete=<?= $row['ID_KE_HOACH'] ?>"
                                class="btn btn-delete btn-sm" onclick="return confirm('Xóa kế hoạch này?')">Xóa</a>
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