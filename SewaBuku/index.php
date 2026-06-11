<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rent-a-Book</title>
    <link rel="stylesheet" href="css/style.css?v=14">
</head>
<body class="landing-body">
    <header class="landing-navbar">
        <a class="landing-brand" href="index.php">
            <span class="landing-brand-icon"></span>
            <strong>Rent-a-Book</strong>
        </a>

        <nav class="landing-menu">
            <a href="#beranda">Beranda</a>
            <a href="#katalog">Katalog</a>
            <a href="#tentang">Tentang</a>
            <a class="landing-login" href="auth/login.php">Login / Daftar</a>
        </nav>
    </header>

    <main class="landing-main" id="beranda">
        <section class="landing-hero">
            <div class="landing-hero-text">
                <h1>Temukan, Sewa, dan Nikmati Ribuan Buku Favoritmu dalam Satu Platform</h1>
                <p>Temukan koleksi buku dari berbagai kategori dan nikmati pengalaman membaca yang lebih hemat dengan sistem penyewaan yang mudah, cepat, dan terpercaya</p>
            </div>

        </section>

        <section class="landing-recommendation" id="katalog">
            <h2>Rekomendasi Buku</h2>

            <div class="landing-book-grid">
                <article class="landing-book-card">
                    <div class="landing-cover cover-bookmark"></div>
                    <h3>Dilan 1990</h3>
                    <a href="auth/login.php">Sewa Sekarang</a>
                </article>

                <article class="landing-book-card">
                    <div class="landing-cover cover-simple"></div>
                    <h3>Good / Bad Fortune</h3>
                    <a href="auth/login.php">Sewa Sekarang</a>
                </article>

                <article class="landing-book-card">
                    <div class="landing-cover cover-open"></div>
                    <h3>Atomic Habits</h3>
                    <a href="auth/login.php">Sewa Sekarang</a>
                </article>

                <article class="landing-book-card">
                    <div class="landing-cover cover-lines"></div>
                    <h3>Earth</h3>
                    <a href="auth/login.php">Sewa Sekarang</a>
                </article>
            </div>
        </section>
    </main>
</body>
</html>
