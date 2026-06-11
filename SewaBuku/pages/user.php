<?php
require '../functions/koneksi.php';
wajibLogin();

if($_SESSION['role'] !== 'user'){
    header("Location: admin/databuku.php");
    exit;
}

header("Location: user/beranda.php");
exit;
?>
