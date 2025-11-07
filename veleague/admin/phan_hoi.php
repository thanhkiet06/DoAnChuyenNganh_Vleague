<?php
require '../auth.php';
require_role('admin');
require '../connect.php';

$id_yeu_cau = $_GET['id'] ?? 0;
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $noi_dung = trim($_POST['noi_dung']);

    if ($noi_dung) {
        $stmt = $conn->prepare("INSERT INTO PHAN_HOI_ADMIN (ID_YEU_CAU, NOI_DUNG) VALUES (?, ?)");
        $stmt->bind_param("is", $id_yeu_cau, $noi_dung);
        $stmt->execute();
        header("Location: ds_yeu_cau.php?msg=phan_hoi_thanh_cong");
        exit;
    }
}
?>

<form method="post" class="container mt-5">
    <h3>Phản hồi yêu cầu #<?= htmlspecialchars($id_yeu_cau) ?></h3>
    <textarea name="noi_dung" class="form-control" rows="5" placeholder="Nội dung phản hồi..."></textarea>
    <button type="submit" class="btn btn-primary mt-3">Gửi phản hồi</button>
</form>
