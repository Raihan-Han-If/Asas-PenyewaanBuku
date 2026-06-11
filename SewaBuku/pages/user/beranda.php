<?php
require '../../functions/koneksi.php';
wajibLogin();

$pesan = "";

if(isset($_POST['sewa'])){
    $result = sewaBuku($_POST['id_buku']);

    if($result === true){
        $pesan = "Buku berhasil diajukan untuk disewa";
    }else{
        $pesan = $result ?: "Gagal menyewa buku";
    }
}

$buku = ambilSemuaBuku();
$pinjamanAktif = ambilPinjamanAktifUser($_SESSION['id']);
$punyaPinjamanAktif = userPunyaPinjamanAktif($_SESSION['id']);
$namaUser = $_SESSION['nama_lengkap'] ?? 'User';
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Buku</title>
    <link rel="stylesheet" href="../../css/style.css?v=15">
</head>
<body class="dashboard-body">
    <?php renderNavbar('user', 'beranda'); ?>

    <main class="user-main">
        <header class="user-topbar user-book-header">
            <h1>Halo, <?= htmlspecialchars($namaUser); ?></h1>
            <label class="book-search">
                <input type="search" id="bookSearch" placeholder="Cari Buku">
                <span></span>
            </label>
        </header>

        <?php if($pesan) : ?>
            <div class="notice"><?= htmlspecialchars($pesan); ?></div>
        <?php endif; ?>

        <section class="book-list" id="bookList">
            <?php if(empty($buku)) : ?>
                <div class="empty-state">
                    <h2>Belum ada buku</h2>
                    <p>Data buku belum tersedia di database.</p>
                </div>
            <?php endif; ?>

            <?php foreach($buku as $item) : ?>
                <?php $statusAktif = $pinjamanAktif[$item['id']] ?? null; ?>
                <article class="book-row" data-title="<?= htmlspecialchars(strtolower($item['judul_buku'] . ' ' . $item['penulis'] . ' ' . $item['Nomor_buku'])); ?>">
                    <div class="book-row-cover">
                        <?php if(!empty($item['cover'])) : ?>
                            <img src="../../img/<?= htmlspecialchars($item['cover']); ?>" alt="<?= htmlspecialchars($item['judul_buku']); ?>">
                        <?php else : ?>
                            <span><?= strtoupper(substr($item['judul_buku'], 0, 1)); ?></span>
                        <?php endif; ?>
                    </div>

                    <div class="book-row-info">
                        <h2><?= htmlspecialchars($item['judul_buku']); ?></h2>
                        <p><?= htmlspecialchars($item['penulis']); ?></p>
                        <strong>Rp 10.000</strong>
                        <small>No.<?= htmlspecialchars(str_pad((string) $item['Nomor_buku'], 8, '0', STR_PAD_LEFT)); ?></small>
                        <form action="" method="post">
                            <input type="hidden" name="id_buku" value="<?= $item['id']; ?>">
                            <button type="submit" name="sewa" class="btn-primary">
                                <?php if($statusAktif === 'pending') : ?>
                                    Menunggu Konfirmasi
                                <?php elseif($statusAktif === 'disewa') : ?>
                                    Dipinjam
                                <?php else : ?>
                                    Sewa
                                <?php endif; ?>
                            </button>
                        </form>
                    </div>
                </article>
            <?php endforeach; ?>
        </section>
    </main>
    <script>
        const searchInput = document.getElementById('bookSearch');
        const bookRows = document.querySelectorAll('.book-row');

        searchInput?.addEventListener('input', function(){
            const keyword = this.value.toLowerCase().trim();

            bookRows.forEach(function(row){
                row.hidden = !row.dataset.title.includes(keyword);
            });
        });
    </script>
</body>
</html>
