<?php
require '../auth.php';
require_role('admin');
require '../connect.php';

$id = $_GET['id'];
$conn->query("DELETE FROM GIAI_DAU WHERE ID_GIAI_DAU = $id");
header("Location: giaidau.php");
