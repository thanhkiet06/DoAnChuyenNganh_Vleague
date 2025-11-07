<?php
require '../auth.php';
require_role('admin');
require '../connect.php';

$id = $_GET['id'];

// Xóa các trận có liên quan đến đội bóng
$conn->query("DELETE FROM TRAN_DAU WHERE ID_DOI_1 = $id OR ID_DOI_2 = $id");

// Xóa cầu thủ thuộc đội bóng (nếu có)
$conn->query("DELETE FROM CAU_THU WHERE ID_DOI_BONG = $id");

// Xóa bảng xếp hạng của đội này (nếu có)
$conn->query("DELETE FROM BANG_XEP_HANG WHERE ID_DOI_BONG = $id");

// Cuối cùng xóa đội bóng
$conn->query("DELETE FROM DOI_BONG WHERE ID_DOI_BONG = $id");

header("Location: doibong.php");
