<?php
require '../auth.php';
require_role('viewer');
require '../connect.php';

if (isset($_GET['id'])) {
    $id_doi_bong = (int)$_GET['id'];
    $id_user = $_SESSION['user_id'];

    // Kiểm tra đã yêu thích chưa
    $check = $conn->query("SELECT * FROM DOI_YEU_THICH WHERE ID_NGUOI_DUNG = $id_user AND ID_DOI_BONG = $id_doi_bong");

    if ($check->num_rows == 0) {
        $conn->query("INSERT INTO DOI_YEU_THICH (ID_NGUOI_DUNG, ID_DOI_BONG) VALUES ($id_user, $id_doi_bong)");
    }
}

header('Location: doibong.php');
exit;
?>
