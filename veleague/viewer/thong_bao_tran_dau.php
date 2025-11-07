<?php
require '../auth.php';
require_role('viewer');
require '../connect.php';

// Lấy danh sách trận đấu trong 7 ngày tới
$today = date('Y-m-d');
$next_week = date('Y-m-d', strtotime('+7 days'));
$trandau = $conn->query("
    SELECT t.*, d1.TEN_DOI_BONG AS DOI_NHA, d2.TEN_DOI_BONG AS DOI_KHACH
    FROM TRAN_DAU t
    JOIN DOI_BONG d1 ON t.ID_DOI_1 = d1.ID_DOI_BONG
    JOIN DOI_BONG d2 ON t.ID_DOI_2 = d2.ID_DOI_BONG
    WHERE t.NGAY_THI_DAU BETWEEN '$today' AND '$next_week'
    ORDER BY t.NGAY_THI_DAU ASC
");

// Lấy danh sách trận đấu đã đăng ký thông báo
$user_id = $_SESSION['user_id'];
$thongbao = $conn->query("SELECT ID_TRAN_DAU FROM THONG_BAO_TRAN_DAU WHERE ID_NGUOI_DUNG = $user_id");
$thongbao_list = [];
while ($row = $thongbao->fetch_assoc()) {
    $thongbao_list[] = $row['ID_TRAN_DAU'];
}

// Xử lý AJAX đăng ký/hủy thông báo
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id_tran'])) {
    header('Content-Type: application/json');
    $id_tran = intval($_POST['id_tran']);
    
    // Kiểm tra trận đấu có tồn tại
    $check = $conn->prepare("SELECT ID_TRAN_DAU FROM TRAN_DAU WHERE ID_TRAN_DAU = ?");
    $check->bind_param("i", $id_tran);
    $check->execute();
    if ($check->get_result()->num_rows === 0) {
        echo json_encode(['success' => false, 'message' => 'Trận đấu không tồn tại']);
        exit;
    }

    try {
        if (in_array($id_tran, $thongbao_list)) {
            // Hủy thông báo
            $stmt = $conn->prepare("DELETE FROM THONG_BAO_TRAN_DAU WHERE ID_NGUOI_DUNG = ? AND ID_TRAN_DAU = ?");
            $stmt->bind_param("ii", $user_id, $id_tran);
            $stmt->execute();
            echo json_encode(['success' => true, 'message' => 'Đã hủy thông báo', 'action' => 'unsubscribed']);
        } else {
            // Đăng ký thông báo
            $stmt = $conn->prepare("INSERT INTO THONG_BAO_TRAN_DAU (ID_NGUOI_DUNG, ID_TRAN_DAU) VALUES (?, ?)");
            $stmt->bind_param("ii", $user_id, $id_tran);
            $stmt->execute();
            echo json_encode(['success' => true, 'message' => 'Đã đăng ký thông báo', 'action' => 'subscribed']);
        }
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => 'Lỗi: ' . $e->getMessage()]);
    }
    exit;
}
?>

<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <title>Thông báo trận đấu - V.League 2025</title>
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

    .btn-notify {
        background-color: #d90429;
        color: white;
    }

    .btn-notify:hover {
        background-color: #b40221;
    }

    .btn-notify:disabled {
        background-color: #cccccc;
        cursor: not-allowed;
    }

    .btn-back {
        background-color: #d90429;
        color: white;
    }

    .btn-back:hover {
        background-color: #b40221;
    }

    .toast-container {
        position: fixed;
        top: 20px;
        right: 20px;
        z-index: 1050;
    }
    </style>
</head>

<body>
    <div class="container">
        <h1 class="text-center title">🔔 Thông báo trận đấu</h1>
        <p class="text-center text-muted mb-4">Đăng ký nhận thông báo cho các trận đấu sắp tới</p>

        <div class="toast-container"></div>

        <?php if ($trandau->num_rows > 0) { ?>
        <div class="row g-3">
            <?php while ($row = $trandau->fetch_assoc()) { ?>
            <div class="col-12">
                <div class="match-card">
                    <h5><?= htmlspecialchars($row['DOI_NHA']) ?> vs <?= htmlspecialchars($row['DOI_KHACH']) ?></h5>
                    <p>Ngày: <?= $row['NGAY_THI_DAU'] ?> | Địa điểm: <?= htmlspecialchars($row['DIA_DIEM']) ?></p>
                    <button class="btn btn-notify btn-sm notify-btn" data-id="<?= $row['ID_TRAN_DAU'] ?>"
                        data-subscribed="<?= in_array($row['ID_TRAN_DAU'], $thongbao_list) ? 'true' : 'false' ?>">
                        <?= in_array($row['ID_TRAN_DAU'], $thongbao_list) ? 'Hủy thông báo' : 'Đăng ký thông báo' ?>
                    </button>
                </div>
            </div>
            <?php } ?>
        </div>
        <?php } else { ?>
        <p class="text-muted text-center">Không có trận đấu nào trong 7 ngày tới.</p>
        <?php } ?>

        <div class="text-center mt-4">
            <a href="index.php" class="btn btn-back px-4">← Về trang chính</a>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
    document.querySelectorAll('.notify-btn').forEach(button => {
        button.addEventListener('click', function() {
            const idTran = this.getAttribute('data-id');
            const isSubscribed = this.getAttribute('data-subscribed') === 'true';

            // Disable button để tránh nhấn liên tục
            this.disabled = true;
            this.textContent = 'Đang xử lý...';

            fetch(window.location.href, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: `id_tran=${idTran}`
                })
                .then(response => response.json())
                .then(data => {
                    this.disabled = false;
                    if (data.success) {
                        // Cập nhật giao diện nút
                        this.setAttribute('data-subscribed', data.action === 'subscribed' ? 'true' :
                            'false');
                        this.textContent = data.action === 'subscribed' ? 'Hủy thông báo' :
                            'Đăng ký thông báo';

                        // Hiển thị toast thông báo
                        showToast(data.message, 'success');
                    } else {
                        showToast(data.message, 'error');
                        this.textContent = isSubscribed ? 'Hủy thông báo' : 'Đăng ký thông báo';
                    }
                })
                .catch(error => {
                    this.disabled = false;
                    this.textContent = isSubscribed ? 'Hủy thông báo' : 'Đăng ký thông báo';
                    showToast('Đã có lỗi xảy ra', 'error');
                });
        });
    });

    function showToast(message, type) {
        const toastContainer = document.querySelector('.toast-container');
        const toast = document.createElement('div');
        toast.className =
            `toast align-items-center text-white ${type === 'success' ? 'bg-success' : 'bg-danger'} border-0`;
        toast.setAttribute('role', 'alert');
        toast.setAttribute('aria-live', 'assertive');
        toast.setAttribute('aria-atomic', 'true');
        toast.innerHTML = `
                <div class="d-flex">
                    <div class="toast-body">${message}</div>
                    <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
                </div>
            `;
        toastContainer.appendChild(toast);
        const bsToast = new bootstrap.Toast(toast);
        bsToast.show();
        setTimeout(() => toast.remove(), 3000);
    }
    </script>
</body>

</html>