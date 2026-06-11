<?php
require '../../functions/koneksi.php';
wajibAdmin();

if(isset($_POST['update_status'])){
    updateStatusPenyewaan([
        'id' => $_POST['id'],
        'status' => $_POST['status']
    ]);
    header("Location: penyewaan.php");
    exit;
}

$penyewaan = ambilSemuaPenyewaan();
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Penyewaan</title>
    <link rel="stylesheet" href="../../css/style.css?v=15">
</head>
<body class="dashboard-body admin-body">
    <?php renderNavbar('admin', 'penyewaan'); ?>

    <main class="user-main">
        <header class="user-topbar">
            <div>
                <h1>Data Penyewa</h1>
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
                            <th>Tanggal Pinjam</th>
                            <th>Tanggal Kembali</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($penyewaan as $index => $row) : ?>
                            <?php $statusTampil = labelStatus($row['status'], $row['tanggal_kembali']); ?>
                            <?php
                                $labelAdmin = 'Tersedia';

                                if($statusTampil === 'disewa'){
                                    $labelAdmin = 'Dipinjam';
                                }elseif($statusTampil === 'dikembalikan'){
                                    $labelAdmin = 'Dikembalikan';
                                }elseif($statusTampil === 'terlambat'){
                                    $labelAdmin = 'Terlambat';
                                }
                            ?>
                            <tr>
                                <td><?= $index + 1; ?></td>
                                <td><?= htmlspecialchars($row['nama_lengkap'] ?? '-'); ?></td>
                                <td><?= htmlspecialchars($row['judul_buku'] ?? '-'); ?></td>
                                <td><?= formatTanggal($row['tanggal_terbit']); ?></td>
                                <td><?= formatTanggal($row['tanggal_kembali']); ?></td>
                                <td>
                                    <span class="status-badge <?= statusBadgeClass($statusTampil); ?>">
                                        <?= htmlspecialchars($labelAdmin); ?>
                                    </span>
                                </td>
                                <td>
                                    <?php if($row['status'] === 'pending') : ?>
                                        <form class="inline-form status-form" action="" method="post">
                                            <input type="hidden" name="id" value="<?= $row['id']; ?>">
                                            <input type="hidden" name="status" value="disewa">
                                            <button class="btn-table" type="submit" name="update_status">Konfirmasi</button>
                                        </form>
                                    <?php elseif(in_array($statusTampil, ['disewa', 'terlambat'], true)) : ?>
                                        <form class="inline-form status-form" action="" method="post">
                                            <input type="hidden" name="id" value="<?= $row['id']; ?>">
                                            <select name="status">
                                                <option value="terlambat" <?= $statusTampil === 'terlambat' ? 'selected' : ''; ?>>Terlambat</option>
                                                <option value="dikembalikan">Dikembalikan</option>
                                            </select>
                                            <button class="btn-table" type="submit" name="update_status">Ubah</button>
                                        </form>
                                    <?php else : ?>
                                        -
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </section>
    </main>
</body>
</html>
