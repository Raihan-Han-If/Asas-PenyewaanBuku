<?php
require '../../functions/koneksi.php';
wajibAdmin();

$id = $_GET['id'] ?? 0;
$buku = ambilBukuById($id);

if(!$buku){
    header("Location: databuku.php");
    exit;
}

$error = "";

if(isset($_POST['simpan'])){
    if(editBuku($_POST, $_FILES)){
        header("Location: databuku.php");
        exit;
    }

    $error = "Buku gagal diubah";
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Buku</title>
    <link rel="stylesheet" href="../../css/style.css?v=11">
</head>
<body class="dashboard-body admin-body admin-form-body">
    <?php renderNavbar('admin', 'databuku'); ?>

    <main class="form-page book-form-page">
        <form class="panel-form book-data-form" action="" method="post" enctype="multipart/form-data">
            <h1>Ubah Buku</h1>
            <?php if($error) : ?><div class="error"><?= $error; ?></div><?php endif; ?>

            <input type="hidden" name="id" value="<?= $buku['id']; ?>">
            <input type="hidden" name="cover_lama" value="<?= htmlspecialchars($buku['cover']); ?>">

            <label class="sr-only" for="Nomor_buku">No. Buku</label>
            <input id="Nomor_buku" type="number" name="Nomor_buku" value="<?= htmlspecialchars($buku['Nomor_buku']); ?>" placeholder="Nomor Buku" required>

            <label class="sr-only" for="judul_buku">Judul Buku</label>
            <input id="judul_buku" type="text" name="judul_buku" value="<?= htmlspecialchars($buku['judul_buku']); ?>" placeholder="Judul Buku" required>

            <label class="sr-only" for="penulis">Penulis</label>
            <input id="penulis" type="text" name="penulis" value="<?= htmlspecialchars($buku['penulis']); ?>" placeholder="Pengarang" required>

            <label class="sr-only" for="cover">Cover</label>
            <input id="cover" class="cover-input" type="file" name="cover" accept="image/*">
            <label class="cover-picker" for="cover" aria-label="Pilih cover buku">
                <span></span>
            </label>

            <button class="btn-black" type="submit" name="simpan">Ubah</button>
            <a class="btn-outline" href="databuku.php">Kembali</a>
        </form>
    </main>
</body>
</html>
