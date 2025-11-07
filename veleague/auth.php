<?php
session_start();
function require_role($role) {
    if (!isset($_SESSION['vai_tro']) || $_SESSION['vai_tro'] != $role) {
        header("Location: ../login.php");
        exit;
    }
}
?>
