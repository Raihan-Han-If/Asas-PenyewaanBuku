<?php
require '../../functions/koneksi.php';
wajibAdmin();

$error = "";

if(isset($_POST['simpan'])){
    if(tambahBuku($_POST, $_FILES)){
        header("Location: databuku.php");
        exit;
    }

    $error = "Buku gagal ditambahkan";
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Buku</title>
    <link rel="stylesheet" href="../../css/style.css?v=11">
</head>
<body class="dashboard-body admin-body admin-form-body">
    <?php renderNavbar('admin', 'databuku'); ?>

    <main class="form-page book-form-page">
        <form class="panel-form book-data-form" action="" method="post" enctype="multipart/form-data">
            <h1>Tambah Buku</h1>
            <?php if($error) : ?><div class="error"><?= $error; ?></div><?php endif; ?>

            <label class="sr-only" for="judul_buku">Judul Buku</label>
            <input id="judul_buku" type="text" name="judul_buku" placeholder="Judul Buku" required>
            
            <label class="sr-only" for="Nomor_buku">No. Buku</label>
            <input id="Nomor_buku" type="number" name="Nomor_buku" placeholder="Nomor Buku" required>

            <label class="sr-only" for="penulis">Penulis</label>
            <input id="penulis" type="text" name="penulis" placeholder="Pengarang" required>

            <label class="sr-only" for="cover">Cover</label>
            <input id="cover" class="cover-input" type="file" name="cover" accept="image/*">
            <label class="cover-picker" for="cover" aria-label="Pilih cover buku">
                <span></span>
            </label>

            <button class="btn-black" type="submit" name="simpan">Tambah</button>
            <a class="btn-outline" href="databuku.php">Kembali</a>
        </form>
    </main>
</body>
</html>
