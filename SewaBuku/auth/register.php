<?php
require '../functions/koneksi.php';

$error = "";
$success = "";

if(isset($_POST['register'])){
    $result = register($_POST);

    if($result === true){
        $success = "Register berhasil. Silakan login.";
    }else{
        $error = $result;
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - Rent-a-Book</title>
    <link rel="stylesheet" href="../css/style.css?v=14">
</head>
<body class="auth-body">
    <header class="auth-navbar">
        <a class="landing-brand" href="../index.php">
            <span class="landing-brand-icon"></span>
            <strong>Rent-a-Book</strong>
        </a>

        <nav class="landing-menu">
            <a href="../index.php">Beranda</a>
            <a href="../index.php#katalog">Katalog</a>
            <a href="../index.php#tentang">Tentang</a>
        </nav>
    </header>

    <div class="auth-page">
        <div class="auth-card">
            <div class="auth-visual" aria-hidden="true">
                <div>
                    <h2>Rent-a-Book</h2>
                    <p>Buat akun baru dan mulai sewa buku favoritmu dengan mudah.</p>
                </div>
            </div>

            <form class="auth-form" action="" method="post">
                <div class="auth-heading">
                    <h1>Buat Akun Baru</h1>
                    <p>Silahkan Isi Persyaratan<br>Sebelum Membuat akun baru</p>
                </div>

                <?php if($error) : ?>
                    <div class="error"><?= $error; ?></div>
                <?php endif; ?>

                <?php if($success) : ?>
                    <div class="success"><?= $success; ?></div>
                <?php endif; ?>

                <input type="text" name="nama_lengkap" id="nama_lengkap" placeholder="Username ( ex : user123 )" required>

                <input type="email" name="email" id="email" placeholder="Email ( ex : user123@gmail.com )" required>

                <input type="number" name="nik" id="nik" placeholder="Nomor Induk Kependudukan ( ex : 123456789 )" required>

                <input type="password" name="password" id="password" placeholder="Password ( ex : Uls2e3r )" required>

                <input type="password" name="confirm_password" id="confirm_password" placeholder="Konfirmasi Password ( ex : Uls2e3r )" required>

                <button type="submit" name="register" class="btn-black">Buat</button>
                <a href="login.php" class="btn-outline">Kembali</a>
            </form>
        </div>
    </div>
</body>
</html>
