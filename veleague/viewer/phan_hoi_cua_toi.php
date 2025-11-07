<?php
require '../auth.php';
require_role('viewer');
require '../connect.php';

$user_id = $_SESSION['user_id'];
$ds = $conn->query("
    SELECT yc.NOI_DUNG AS yeu_cau, yc.NGAY_GUI, ph.NOI_DUNG AS phan_hoi, ph.NGAY_PHAN_HOI
    FROM YEU_CAU_USER yc
    LEFT JOIN PHAN_HOI_ADMIN ph ON yc.ID_YEU_CAU = ph.ID_YEU_CAU
    WHERE yc.ID_NGUOI_DUNG = $user_id
    ORDER BY yc.NGAY_GUI DESC
");
?>

<div class="container mt-5">
    <h2>📩 Phản hồi từ quản trị viên</h2>
    <table class="table table-bordered mt-3">
        <thead>
            <tr>
                <th>Nội dung yêu cầu</th>
                <th>Phản hồi</th>
                <th>Ngày gửi</th>
                <th>Ngày phản hồi</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = $ds->fetch_assoc()): ?>
                <tr>
                    <td><?= htmlspecialchars($row['yeu_cau']) ?></td>
                    <td><?= $row['phan_hoi'] ? htmlspecialchars($row['phan_hoi']) : '<span class="text-muted">Chưa phản hồi</span>' ?></td>
                    <td><?= $row['NGAY_GUI'] ?></td>
                    <td><?= $row['NGAY_PHAN_HOI'] ?? '-' ?></td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>
