<?php
require '../../functions/koneksi.php';
wajibLogin();

$namaUser = $_SESSION['nama_lengkap'] ?? 'User';

if(isset($_POST['kembalikan'])){
    kembalikanBuku($_POST['id_penyewaan']);
    header("Location: bukusaya.php");
    exit;
}

$pinjaman = ambilPenyewaanUser($_SESSION['id']);

$totalDisewa = 0;
$totalPending = 0;
$totalTerlambat = 0;

foreach($pinjaman as $item){
    $statusHitung = labelStatus($item['status'], $item['tanggal_kembali']);

    if($statusHitung === 'disewa'){
        $totalDisewa++;
    }

    if($statusHitung === 'pending'){
        $totalPending++;
    }

    if($statusHitung === 'terlambat'){
        $totalTerlambat++;
    }
}

?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Buku Pinjaman</title>
    <link rel="stylesheet" href="../../css/style.css?v=14">
</head>
<body class="dashboard-body">
    <?php renderNavbar('user', 'bukusaya'); ?>

    <main class="user-main borrowed-main">
        <header class="borrowed-header">
            <h1>Buku Yang Telah Anda Pinjam</h1>
        </header>

        <?php if(empty($pinjaman)) : ?>
            <div class="empty-state borrowed-empty">
                <h2>Belum ada pinjaman</h2>
                <p>Pilih buku di halaman beranda untuk mulai menyewa.</p>
            </div>
        <?php endif; ?>

        <section class="borrowed-list">
            <?php foreach($pinjaman as $row) : ?>
                <?php
                    $statusTampil = labelStatus($row['status'], $row['tanggal_kembali']);
                    $hariTerlambat = hitungHariTerlambat($row['tanggal_kembali']);
                    $totalDenda = $statusTampil === 'terlambat' ? max(3000, hitungDenda($row['tanggal_kembali'])) : 0;
                    $cover = !empty($row['cover']) ? '../../img/' . htmlspecialchars($row['cover']) : '';
                    $buttonClass = 'pending';
                    $buttonText = 'Menunggu Konfirmasi';
                    $buttonDisabled = true;

                    if($statusTampil === 'disewa'){
                        $buttonClass = 'borrowed';
                        $buttonText = 'Kembalikan';
                        $buttonDisabled = false;
                    }elseif($statusTampil === 'terlambat'){
                        $buttonClass = 'late';
                        $buttonText = 'Terlambat ( Bayar Denda )';
                        $buttonDisabled = false;
                    }elseif($statusTampil === 'dikembalikan'){
                        $buttonClass = 'done';
                        $buttonText = 'Dikembalikan';
                    }
                ?>
                <article class="borrowed-item">
                    <div class="borrowed-cover">
                        <?php if($cover) : ?>
                            <img src="<?= $cover; ?>" alt="<?= htmlspecialchars($row['judul_buku']); ?>">
                        <?php else : ?>
                            <span><?= strtoupper(substr($row['judul_buku'], 0, 1)); ?></span>
                        <?php endif; ?>
                    </div>

                    <div class="borrowed-info">
                        <h2><?= htmlspecialchars($row['judul_buku']); ?></h2>
                        <p><?= htmlspecialchars($row['penulis']); ?></p>
                        <strong>Tanggal Pinjam&nbsp; : <?= formatTanggal($row['tanggal_terbit']); ?></strong>
                        <strong>Tanggal Kembali : <?= formatTanggal($row['tanggal_kembali']); ?></strong>

                        <?php if($statusTampil === 'terlambat') : ?>
                            <small>Total Denda: Rp <?= number_format($totalDenda, 0, ',', '.'); ?> (<?= max(1, $hariTerlambat); ?> hari telat)</small>
                        <?php endif; ?>

                        <form action="" method="post">
                            <input type="hidden" name="id_penyewaan" value="<?= $row['id']; ?>">
                            <button class="borrowed-action <?= $buttonClass; ?>" type="submit" name="kembalikan" <?= $buttonDisabled ? 'disabled' : ''; ?>>
                                <?= htmlspecialchars($buttonText); ?>
                            </button>
                        </form>
                    </div>
                </article>
            <?php endforeach; ?>
        </section>
    </main>
</body>
</html>
