<?php
require '../auth.php';
require_role('admin');
require '../connect.php';

if (isset($_GET['id_tran'])) {
    $id_tran = (int)$_GET['id_tran'];
    $conn->query("DELETE FROM BINH_CHON_CAU_THU WHERE ID_TRAN_DAU = $id_tran");
    header("Location: thongke_binhchon.php?reset=ok");
    exit;
} else {
    header("Location: thongke_binhchon.php");
}
?>
