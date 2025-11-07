<?php
require '../auth.php';
require_role('viewer');
require '../connect.php';

if (isset($_GET['id'])) {
    $id_doi_bong = (int)$_GET['id'];
    $id_user = $_SESSION['user_id'];

    $conn->query("DELETE FROM DOI_YEU_THICH WHERE ID_NGUOI_DUNG = $id_user AND ID_DOI_BONG = $id_doi_bong");
}

header('Location: yeuthich.php');
exit;
?>
