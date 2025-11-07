<?php
require '../auth.php';
require_role('admin');
require '../connect.php';

if (isset($_GET['id'])) {
    $id = (int) $_GET['id'];
    $conn->query("DELETE FROM BINH_LUAN WHERE ID_BINH_LUAN = $id");
}

header("Location: binhluan.php");
exit;
