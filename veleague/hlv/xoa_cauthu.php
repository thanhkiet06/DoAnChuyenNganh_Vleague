<?php
require '../auth.php';
require_role('hlv');
require '../connect.php';

$id = $_GET['id'];
$conn->query("DELETE FROM CAU_THU WHERE ID_CAU_THU = $id");
header("Location: cauthu.php");
