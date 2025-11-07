<?php
require '../auth.php';
require_role('admin');
require '../connect.php';

$id = $_GET['id'];
$conn->query("DELETE FROM NGUOI_DUNG WHERE ID_NGUOI_DUNG = $id");
header("Location: nguoidung.php");
