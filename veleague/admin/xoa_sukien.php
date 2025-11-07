<?php
require '../auth.php';
require_role('admin');
require '../connect.php';

$id = $_GET['id'];
$id_tran = $_GET['id_tran'];

$conn->query("DELETE FROM SU_KIEN_TRAN_DAU WHERE ID_SU_KIEN = $id");

header("Location: sukien_tran_detail.php?id_tran=$id_tran");
exit;
