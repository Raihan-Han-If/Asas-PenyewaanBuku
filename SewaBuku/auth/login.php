<?php
require '../functions/koneksi.php';

$error = "";

if(isset($_POST['login'])){
    $result = login($_POST);

    if($result){
        if($result === 'admin'){
            header("Location: ../pages/admin.php");
        }else{
            header("Location: ../pages/user/beranda.php");
        }
        exit;
    }

    $error = "Email/nama atau password salah";
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Rent-a-Book</title>
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
                    <p>Masuk untuk melihat katalog dan mengelola pinjaman bukumu.</p>
                </div>
            </div>

            <form class="auth-form" action="" method="post">
                <div class="auth-heading">
                    <h1>Selamat Datang</h1>
                    <p>Silahkan Login Kembali<br>Untuk Melanjutkan Peminjaman Buku</p>
                </div>

                <?php if($error) : ?>
                    <div class="error"><?= $error; ?></div>
                <?php endif; ?>

                <label for="username">Username or Email</label>
                <input type="text" name="username" id="username" placeholder="( ex : user123 ) or ( ex : user123@gmail.com )" required>

                <label for="password">Password</label>
                <input type="password" name="password" id="password" placeholder="( ex : uls2e3r )" required>

                <button type="submit" name="login" class="btn-black">Login</button>
                <a href="../index.php" class="btn-outline">Kembali</a>
                <p class="auth-link-text">Baru? <a href="register.php">Daftar Sekarang</a> - dan mulailah menyewa buku!</p>
            </form>
        </div>
    </div>
</body>
</html>
