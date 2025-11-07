<?php
// player_messages.php
require '../connect.php';
require '../auth.php';   // giả sử file auth.php khởi động session và kiểm tra auth

// Cho phép vai trò 'viewer' hoặc 'cau_thu' (tùy cách bạn đặt vai trò)
session_start();
$role = $_SESSION['vai_tro'] ?? '';
if (!in_array($role, ['viewer', 'cau_thu', 'player'])) {
    // không có quyền -> chuyển hướng về login
    header('Location: ../login.php');
    exit;
}

$id_cauthu = intval($_SESSION['user_id']);

// Lấy ID đội bóng của cầu thủ
$stmt = $conn->prepare("SELECT ID_DOI_BONG FROM CAU_THU WHERE ID_CAU_THU = ?");
$stmt->bind_param("i", $id_cauthu);
$stmt->execute();
$res = $stmt->get_result();
$teamRow = $res->fetch_assoc();

if (!$teamRow) {
    echo "<div class='container mt-5'><p>Bạn chưa thuộc đội bóng nào.</p><a href='../index.php' class='btn btn-secondary mt-2'>Quay lại</a></div>";
    exit;
}

$id_doi = $teamRow['ID_DOI_BONG'];

// Đánh dấu các tin nhắn gửi riêng cho cầu thủ này là đã xem
$upd = $conn->prepare("UPDATE TIN_NHAN_NOI_BO SET DA_XEM = 1 WHERE ID_DOI_BONG = ? AND ID_NGUOI_NHAN = ? AND DA_XEM = 0");
$upd->bind_param("ii", $id_doi, $id_cauthu);
$upd->execute();

// Lấy danh sách tin nhắn: cả đội (ID_NGUOI_NHAN IS NULL) và tin nhắn riêng
$query = "
    SELECT tn.*, u.TEN_DANG_NHAP AS TEN_NGUOI_GUI, u.ID_NGUOI_DUNG AS ID_NGUOI_GUI
    FROM TIN_NHAN_NOI_BO tn
    LEFT JOIN NGUOI_DUNG u ON tn.ID_NGUOI_GUI = u.ID_NGUOI_DUNG
    WHERE tn.ID_DOI_BONG = ? AND (tn.ID_NGUOI_NHAN IS NULL OR tn.ID_NGUOI_NHAN = ?)
    ORDER BY tn.NGAY_GUI DESC
";
$stmt2 = $conn->prepare($query);
$stmt2->bind_param("ii", $id_doi, $id_cauthu);
$stmt2->execute();
$tinnhan = $stmt2->get_result();
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="utf-8">
    <title>Tin nhắn - Cầu thủ</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background:#f7f9fb; font-family: Inter, sans-serif; }
        .container { padding: 40px 20px; max-width:900px; }
        h2 { font-family: 'Bebas Neue', cursive; font-size: 36px; color:#d90429; margin-bottom:20px; }
        .message-card { background:#fff; padding:15px; border-radius:8px; box-shadow:0 4px 10px rgba(0,0,0,0.05); margin-bottom:12px; }
        .badge-unread { background:#d90429; color:#fff; }
        .meta { font-size:0.9rem; color:#666; }
    </style>
</head>
<body>
<div class="container">
    <h2>💬 Tin nhắn nội bộ</h2>
    <p>Xin chào <strong><?= htmlspecialchars($_SESSION['ten_dang_nhap'] ?? 'Người chơi') ?></strong> — đây là tin nhắn gửi tới đội và tin nhắn gửi riêng cho bạn.</p>

    <?php if ($tinnhan->num_rows === 0): ?>
        <div class="alert alert-info">Hiện chưa có tin nhắn nào.</div>
    <?php else: ?>
        <?php while ($row = $tinnhan->fetch_assoc()): ?>
            <div class="message-card">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <strong>Người gửi:</strong>
                        <?= $row['TEN_NGUOI_GUI'] ? htmlspecialchars($row['TEN_NGUOI_GUI']) : 'Hệ thống / HLV' ?>
                        <?php if (is_null($row['ID_NGUOI_NHAN'])): ?>
                            <span class="badge bg-secondary ms-2">Cả đội</span>
                        <?php else: ?>
                            <?php if (intval($row['ID_NGUOI_NHAN']) === $id_cauthu): ?>
                                <span class="badge badge-unread ms-2">Gửi riêng bạn</span>
                            <?php endif; ?>
                        <?php endif; ?>
                    </div>
                    <div class="meta text-end">
                        <div><?= $row['NGAY_GUI'] ?></div>
                        <div><?= $row['DA_XEM'] ? '<small class="text-success">Đã xem</small>' : '<small class="text-danger">Chưa xem</small>' ?></div>
                    </div>
                </div>

                <hr>

                <p><?= nl2br(htmlspecialchars($row['NOI_DUNG'])) ?></p>
            </div>
        <?php endwhile; ?>
    <?php endif; ?>

    <a href="../index.php" class="btn btn-secondary mt-3">← Quay lại trang chính</a>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
