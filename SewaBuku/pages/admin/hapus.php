<?php
require '../../functions/koneksi.php';
wajibAdmin();

if(isset($_GET['id'])){
    hapusBuku($_GET['id']);
}

header("Location: databuku.php");
exit;
?>
