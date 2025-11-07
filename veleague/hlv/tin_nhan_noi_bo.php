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
$id_nguoi_gui = $_SESSION['user_id'];

// Lấy danh sách cầu thủ
$cauthu = $conn->query("SELECT ID_CAU_THU, HO_TEN FROM CAU_THU WHERE ID_DOI_BONG = $id_doi");

// Lấy danh sách tin nhắn đã gửi
$tinnhan = $conn->query("
    SELECT tn.*, c.HO_TEN
    FROM TIN_NHAN_NOI_BO tn
    LEFT JOIN CAU_THU c ON tn.ID_NGUOI_NHAN = c.ID_CAU_THU
    WHERE tn.ID_NGUOI_GUI = $id_nguoi_gui AND tn.ID_DOI_BONG = $id_doi
    ORDER BY tn.NGAY_GUI DESC
");

// Xử lý gửi tin nhắn
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['gui_tinnhan'])) {
    $id_nguoi_nhan = !empty($_POST['id_nguoi_nhan']) ? intval($_POST['id_nguoi_nhan']) : null;
    $noi_dung = trim($_POST['noi_dung']);
    $ngay_gui = date('Y-m-d H:i:s');

    if (!empty($noi_dung)) {
        $stmt = $conn->prepare("INSERT INTO TIN_NHAN_NOI_BO (ID_NGUOI_GUI, ID_NGUOI_NHAN, ID_DOI_BONG, NOI_DUNG, NGAY_GUI) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("iiiss", $id_nguoi_gui, $id_nguoi_nhan, $id_doi, $noi_dung, $ngay_gui);
        if ($stmt->execute()) {
            $success = "Gửi tin nhắn thành công!";
        } else {
            $error = "Lỗi khi gửi tin nhắn.";
        }
    } else {
        $error = "Vui lòng nhập nội dung tin nhắn.";
    }
}
?>

<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <title>Tin nhắn nội bộ - HLV</title>
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

    .btn-gui {
        background-color: #d90429;
        color: white;
    }

    .btn-gui:hover {
        background-color: #b40322;
    }

    .message-card {
        background: white;
        padding: 15px;
        border-radius: 8px;
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.05);
        margin-bottom: 15px;
    }
    </style>
</head>

<body>
    <div class="container">
        <h2>💬 Tin nhắn nội bộ</h2>

        <!-- Form gửi tin nhắn -->
        <div class="form-section">
            <h4>Gửi tin nhắn mới</h4>
            <?php if (isset($success)) { ?>
            <div class="alert alert-success"><?= $success ?></div>
            <?php } elseif (isset($error)) { ?>
            <div class="alert alert-danger"><?= $error ?></div>
            <?php } ?>
            <form method="POST" class="needs-validation" novalidate>
                <div class="mb-3">
                    <label class="form-label">Người nhận</label>
                    <select name="id_nguoi_nhan" class="form-select">
                        <option value="">Cả đội</option>
                        <?php while ($row = $cauthu->fetch_assoc()) { ?>
                        <option value="<?= $row['ID_CAU_THU'] ?>"><?= htmlspecialchars($row['HO_TEN']) ?></option>
                        <?php } ?>
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label">Nội dung</label>
                    <textarea name="noi_dung" class="form-control" rows="5" placeholder="Nhập nội dung tin nhắn..."
                        required></textarea>
                </div>
                <button type="submit" name="gui_tinnhan" class="btn btn-gui">Gửi tin nhắn</button>
            </form>
        </div>

        <!-- Danh sách tin nhắn -->
        <h4>Tin nhắn đã gửi</h4>
        <?php while ($row = $tinnhan->fetch_assoc()) { ?>
        <div class="message-card">
            <p><strong>Người nhận:</strong> <?= $row['ID_NGUOI_NHAN'] ? htmlspecialchars($row['HO_TEN']) : 'Cả đội' ?>
            </p>
            <p><strong>Nội dung:</strong> <?= htmlspecialchars($row['NOI_DUNG']) ?></p>
            <p><strong>Thời gian:</strong> <?= $row['NGAY_GUI'] ?></p>
            <p><strong>Trạng thái:</strong> <?= $row['DA_XEM'] ? 'Đã xem' : 'Chưa xem' ?></p>
        </div>
        <?php } ?>

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