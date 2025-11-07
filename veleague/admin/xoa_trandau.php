<?php
require '../auth.php';
require_role('admin');
require '../connect.php';
require '../update_bxh.php';

$id = $_GET['id'];
$tran = $conn->query("SELECT * FROM TRAN_DAU WHERE ID_TRAN_DAU = $id")->fetch_assoc();

if ($tran) {
    $doi1 = $tran['ID_DOI_1'];
    $doi2 = $tran['ID_DOI_2'];
    $bt1 = $tran['BAN_THANG_DOI_1'];
    $bt2 = $tran['BAN_THANG_DOI_2'];

    rollback_bxh($conn, $doi1, $doi2, $bt1, $bt2);
}

$conn->query("DELETE FROM TRAN_DAU WHERE ID_TRAN_DAU = $id");
header("Location: trandau.php");
