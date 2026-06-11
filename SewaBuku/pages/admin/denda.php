<?php
require '../../functions/koneksi.php';
wajibAdmin();

$semuaPenyewaan = ambilSemuaPenyewaan();
$terlambat = [];

foreach($semuaPenyewaan as $row){
    if(labelStatus($row['status'], $row['tanggal_kembali']) === 'terlambat'){
        $terlambat[] = $row;
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Denda</title>
    <link rel="stylesheet" href="../../css/style.css?v=12">
</head>
<body class="dashboard-body admin-body">
    <?php renderNavbar('admin', 'denda'); ?>

    <main class="user-main">
        <header class="user-topbar">
            <div>
                <h1>Data Denda</h1>
            </div>
        </header>

        <section class="table-section">
            <div class="table-wrap">
                <table class="loan-table">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Nama Lengkap</th>
                            <th>Judul Buku</th>
                            <th>Telat</th>
                            <th>Denda</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($terlambat as $index => $row) : ?>
                            <?php
                                $hari = max(1, hitungHariTerlambat($row['tanggal_kembali']));
                                $denda = max(3000, hitungDenda($row['tanggal_kembali']));
                            ?>
                            <tr>
                                <td><?= $index + 1; ?></td>
                                <td><?= htmlspecialchars($row['nama_lengkap'] ?? '-'); ?></td>
                                <td><?= htmlspecialchars($row['judul_buku'] ?? '-'); ?></td>
                                <td><?= $hari; ?> hari</td>
                                <td>Rp <?= number_format($denda, 0, ',', '.'); ?></td>
                                <td>
                                    <span class="status-badge status-late">Terlambat</span>
                                </td>
                            </tr>
                        <?php endforeach; ?>

                        <?php if(empty($terlambat)) : ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </section>
    </main>
</body>
</html>
