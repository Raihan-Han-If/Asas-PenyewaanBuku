<?php
require '../../functions/koneksi.php';
wajibAdmin();

$buku = ambilSemuaBuku();
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Buku</title>
    <link rel="stylesheet" href="../../css/style.css?v=9">
</head>
<body class="dashboard-body admin-body">
    <?php renderNavbar('admin', 'databuku'); ?>

    <main class="user-main">
        <header class="user-topbar">
            <div>
                <h1>Data Buku</h1>
            </div>
            <a href="tambah.php" class="btn-primary">+ Tambah Buku</a>
        </header>

        <section class="table-section">
            <div class="table-wrap">
                <table class="loan-table">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Judul Buku</th>
                            <th>No. Buku</th>
                            <th>Penulis</th>
                            <th>Cover</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($buku as $index => $row) : ?>
                            <tr>
                                <td><?= $index + 1; ?></td>
                                <td><?= htmlspecialchars($row['judul_buku']); ?></td>
                                <td><?= htmlspecialchars($row['Nomor_buku']); ?></td>
                                <td><?= htmlspecialchars($row['penulis']); ?></td>
                                <td><img src="../../img/<?= htmlspecialchars($row['cover']); ?>" width="60"></td>
                                <td>
                                    <a class="btn-table" href="edit.php?id=<?= $row['id']; ?>">Edit</a>
                                    <a class="btn-table danger" href="hapus.php?id=<?= $row['id']; ?>" onclick="return confirm('Hapus buku ini?')">Hapus</a>
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
