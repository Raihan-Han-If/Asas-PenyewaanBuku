<?php
session_start();

$conn = mysqli_connect("localhost", "root", "", "sewabuku");

if(!$conn){
    die("Koneksi gagal: " . mysqli_connect_error());
}

function query($query)
{
    global $conn;

    $result = mysqli_query($conn, $query);
    $rows = [];

    if(!$result){
        return $rows;
    }

    while($row = mysqli_fetch_assoc($result)){
        $rows[] = $row;
    }

    return $rows;
}

function login($data)
{
    global $conn;

    $username = mysqli_real_escape_string($conn, $data['username']);
    $password = mysqli_real_escape_string($conn, $data['password']);

    $result = mysqli_query($conn, "
        SELECT * FROM users
        WHERE email = '$username'
        OR nama_lengkap = '$username'
        LIMIT 1
    ");

    if(mysqli_num_rows($result) === 1){
        $user = mysqli_fetch_assoc($result);

        if($password === $user['password']){
            $_SESSION['id'] = $user['id'];
            $_SESSION['nama_lengkap'] = $user['nama_lengkap'];
            $_SESSION['email'] = $user['email'];
            $_SESSION['role'] = $user['role'];

            return $user['role'];
        }
    }

    return false;
}

function register($data)
{
    global $conn;

    $nama = mysqli_real_escape_string($conn, htmlspecialchars($data['nama_lengkap']));
    $email = mysqli_real_escape_string($conn, htmlspecialchars($data['email']));
    $nik = mysqli_real_escape_string($conn, htmlspecialchars($data['nik']));
    $password = mysqli_real_escape_string($conn, $data['password']);
    $confirmPassword = $data['confirm_password'];

    if($password !== $confirmPassword){
        return "Password tidak sama";
    }

    $cekEmail = mysqli_query($conn, "SELECT id FROM users WHERE email = '$email'");

    if(mysqli_num_rows($cekEmail) > 0){
        return "Email sudah digunakan";
    }

    mysqli_query($conn, "
        INSERT INTO users (nama_lengkap, email, nik, password, role)
        VALUES ('$nama', '$email', '$nik', '$password', 'user')
    ");

    if(mysqli_affected_rows($conn) > 0){
        return true;
    }

    return "Registrasi gagal";
}

function wajibLogin()
{
    if(!isset($_SESSION['id'])){
        header("Location: ../../auth/login.php");
        exit;
    }
}

function ambilSemuaBuku()
{
    return query("SELECT * FROM buku ORDER BY id DESC");
}

function ambilPenyewaanUser($idUser)
{
    global $conn;

    $idUser = mysqli_real_escape_string($conn, $idUser);

    return query("
        SELECT
            penyewaan.id,
            penyewaan.tanggal_terbit,
            penyewaan.tanggal_kembali,
            penyewaan.tanggal_dikembalikan,
            penyewaan.status,
            buku.judul_buku,
            buku.Nomor_buku,
            buku.penulis,
            buku.cover
        FROM penyewaan
        INNER JOIN buku ON penyewaan.id_buku = buku.id
        WHERE penyewaan.id_user = '$idUser'
        ORDER BY penyewaan.id DESC
    ");
}

function ambilPinjamanAktifUser($idUser)
{
    global $conn;

    $idUser = mysqli_real_escape_string($conn, $idUser);
    $result = mysqli_query($conn, "
        SELECT id_buku, status
        FROM penyewaan
        WHERE id_user = '$idUser'
        AND status IN ('pending', 'disewa')
    ");

    $pinjaman = [];

    while($row = mysqli_fetch_assoc($result)){
        $pinjaman[$row['id_buku']] = $row['status'];
    }

    return $pinjaman;
}

function userPunyaPinjamanAktif($idUser)
{
    global $conn;

    $idUser = mysqli_real_escape_string($conn, $idUser);
    $result = mysqli_query($conn, "
        SELECT id FROM penyewaan
        WHERE id_user = '$idUser'
        AND status IN ('pending', 'disewa')
        LIMIT 5
    ");

    return mysqli_num_rows($result) > 0;
}

function sewaBuku($idBuku)
{
    global $conn;

    if(!isset($_SESSION['id'])){
        return false;
    }

    $idUser = mysqli_real_escape_string($conn, $_SESSION['id']);
    $idBuku = mysqli_real_escape_string($conn, $idBuku);

    $tanggalPinjam = date("Y-m-d");
    $tanggalKembali = date("Y-m-d", strtotime("+7 days"));

    // Cek apakah buku yang sama masih dipinjam user
    $cek = mysqli_query($conn, "
        SELECT id
        FROM penyewaan
        WHERE id_user = '$idUser'
        AND id_buku = '$idBuku'
        AND status IN ('pending','disewa')
        LIMIT 1
    ");

    if(mysqli_num_rows($cek) > 0){
        return "Buku ini sudah ada di daftar pinjaman kamu";
    }

    mysqli_query($conn, "
        INSERT INTO penyewaan
        (
            id_user,
            id_buku,
            tanggal_terbit,
            tanggal_kembali,
            tanggal_dikembalikan,
            status
        )
        VALUES
        (
            '$idUser',
            '$idBuku',
            '$tanggalPinjam',
            '$tanggalKembali',
            NULL,
            'pending'
        )
    ");

    return mysqli_affected_rows($conn) > 0;
}

function kembalikanBuku($idPenyewaan)
{
    global $conn;

    if(!isset($_SESSION['id'])){
        return false;
    }

    $idPenyewaan = (int) $idPenyewaan;
    $idUser = mysqli_real_escape_string($conn, $_SESSION['id']);
    $tanggalDikembalikan = date('Y-m-d');

    mysqli_query($conn, "
        UPDATE penyewaan SET
            status = 'dikembalikan',
            tanggal_dikembalikan = '$tanggalDikembalikan'
        WHERE id = $idPenyewaan
        AND id_user = '$idUser'
        AND status IN ('pending', 'disewa', 'terlambat')
    ");

    return mysqli_affected_rows($conn) > 0;
}

function hitungHariTerlambat($tanggalKembali)
{
    if(empty($tanggalKembali) || $tanggalKembali === "0000-00-00"){
        return 0;
    }

    $selisih = floor((strtotime(date('Y-m-d')) - strtotime($tanggalKembali)) / 86400);

    return max(0, $selisih);
}

function hitungDenda($tanggalKembali)
{
    return hitungHariTerlambat($tanggalKembali) * 3000;
}

function formatTanggal($tanggal)
{
    if(empty($tanggal) || $tanggal === "0000-00-00"){
        return "-";
    }

    return date("d M Y", strtotime($tanggal));
}

function labelStatus($status, $tanggalKembali)
{
    $status = strtolower((string) $status);

    if($status === "disewa" && hitungHariTerlambat($tanggalKembali) > 0){
        return "terlambat";
    }

    if($status === ""){
        return "pending";
    }

    return $status;
}

function statusBadgeClass($status)
{
    if($status === 'dikembalikan'){
        return 'status-done';
    }

    if($status === 'terlambat'){
        return 'status-late';
    }

    if($status === 'pending'){
        return 'status-pending';
    }

    return 'status-borrowed';
}

function wajibAdmin()
{
    if(!isset($_SESSION['id']) || $_SESSION['role'] !== 'admin'){
        header("Location: ../../auth/login.php");
        exit;
    }
}

function renderNavbar($role, $active)
{
    $isAdmin = $role === 'admin';
    $scriptDir = str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? ''));
    $basePath = preg_replace('#/pages(/admin|/user)?$#', '', $scriptDir);
    $basePath = rtrim($basePath, '/');
    $brand = 'Rent-a-Book';
    $subtitle = $isAdmin ? 'Admin Panel' : 'User Panel';
    $icon = $isAdmin ? 'A' : 'B';
    $links = $isAdmin
        ? [
            ['url' => $basePath . '/pages/admin/databuku.php', 'label' => 'Data Buku', 'key' => 'databuku'],
            ['url' => $basePath . '/pages/admin/penyewaan.php', 'label' => 'Data Penyewa', 'key' => 'penyewaan'],
            ['url' => $basePath . '/pages/admin/denda.php', 'label' => 'Denda', 'key' => 'denda'],
        ]
        : [
            ['url' => $basePath . '/pages/user/beranda.php', 'label' => 'Daftar Buku', 'key' => 'beranda'],
            ['url' => $basePath . '/pages/user/bukusaya.php', 'label' => 'Buku Yang Dipinjam', 'key' => 'bukusaya'],
        ];
    ?>
    <header class="app-navbar <?= $isAdmin ? 'admin-navbar' : 'user-navbar'; ?>">
        <a class="navbar-brand" href="<?= $isAdmin ? $basePath . '/pages/admin/databuku.php' : $basePath . '/pages/user/beranda.php'; ?>">
            <span class="brand-icon"><?= $icon; ?></span>
            <span>
                <strong><?= $brand; ?></strong>
                <small><?= $subtitle; ?></small>
            </span>
        </a>

        <nav class="navbar-menu">
            <span><?= htmlspecialchars($_SESSION['nama_lengkap'] ?? $brand); ?></span>
            <a href="<?= $basePath; ?>/auth/logout.php" class="nav-logout">Log Out</a>
        </nav>
    </header>

    <aside class="sidebar-user <?= $isAdmin ? 'admin-sidebar' : 'user-sidebar'; ?>">
        <div class="sidebar-title">
            <span class="sidebar-home-icon"></span>
            <strong><?= $isAdmin ? 'Halaman Admin' : 'Halaman User'; ?></strong>
        </div>
        <nav class="sidebar-menu">
            <?php foreach($links as $link) : ?>
                <a href="<?= $link['url']; ?>" class="<?= $active === $link['key'] ? 'active' : ''; ?>">
                    <?= $link['label']; ?>
                </a>
            <?php endforeach; ?>
        </nav>
    </aside>
    <?php
}

function uploadCover($file, $fallback = 'default.jpg')
{
    if(empty($file) || $file['error'] === UPLOAD_ERR_NO_FILE){
        return ['success' => true, 'filename' => $fallback];
    }

    if($file['error'] !== UPLOAD_ERR_OK){
        return ['success' => false, 'message' => 'Upload cover gagal'];
    }

    $allowedExtensions = ['jpg', 'jpeg', 'png', 'webp', 'gif'];
    $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));

    if(!in_array($extension, $allowedExtensions, true)){
        return ['success' => false, 'message' => 'Cover harus berupa JPG, PNG, WEBP, atau GIF'];
    }

    if($file['size'] > 2 * 1024 * 1024){
        return ['success' => false, 'message' => 'Ukuran cover maksimal 2MB'];
    }

    $targetDir = __DIR__ . '/../img/';

    if(!is_dir($targetDir)){
        mkdir($targetDir, 0777, true);
    }

    $filename = 'cover_' . time() . '_' . mt_rand(1000, 9999) . '.' . $extension;

    if(!move_uploaded_file($file['tmp_name'], $targetDir . $filename)){
        return ['success' => false, 'message' => 'Cover gagal disimpan'];
    }

    return ['success' => true, 'filename' => $filename];
}

function tambahBuku($data, $files = [])
{
    global $conn;

    $judul = mysqli_real_escape_string($conn, htmlspecialchars($data['judul_buku']));
    $nomorBuku = (int) $data['Nomor_buku'];
    $penulis = mysqli_real_escape_string($conn, htmlspecialchars($data['penulis']));
    $upload = uploadCover($files['cover'] ?? null);

    if(!$upload['success']){
        return $upload['message'];
    }

    $namaFile = mysqli_real_escape_string($conn, $upload['filename']);

    mysqli_query($conn, "
        INSERT INTO buku (judul_buku, Nomor_buku, penulis, cover)
        VALUES ('$judul', '$nomorBuku', '$penulis', '$namaFile')
    ");

    return mysqli_affected_rows($conn) > 0;
}

function ambilBukuById($id)
{
    $id = (int) $id;
    $data = query("SELECT * FROM buku WHERE id = $id LIMIT 1");

    return $data[0] ?? null;
}

function editBuku($data, $files = [])
{
    global $conn;

    $id = (int) $data['id'];
    $judul = mysqli_real_escape_string($conn, htmlspecialchars($data['judul_buku']));
    $nomorBuku = (int) $data['Nomor_buku'];
    $penulis = mysqli_real_escape_string($conn, htmlspecialchars($data['penulis']));
    $coverLama = $data['cover_lama'] ?? 'default.jpg';
    $upload = uploadCover($files['cover'] ?? null, $coverLama);

    if(!$upload['success']){
        return $upload['message'];
    }

    $cover = mysqli_real_escape_string($conn, $upload['filename']);

    mysqli_query($conn, "
        UPDATE buku SET
            judul_buku = '$judul',
            Nomor_buku = '$nomorBuku',
            penulis = '$penulis',
            cover = '$cover'
        WHERE id = $id
    ");

    return mysqli_affected_rows($conn) >= 0;
}

function hapusBuku($id)
{
    global $conn;

    $id = (int) $id;
    mysqli_query($conn, "DELETE FROM buku WHERE id = $id");

    return mysqli_affected_rows($conn) > 0;
}

function ambilSemuaPenyewaan()
{
    return query("
        SELECT
            penyewaan.*,
            users.nama_lengkap,
            buku.judul_buku,
            buku.Nomor_buku,
            buku.penulis
        FROM penyewaan
        LEFT JOIN users ON penyewaan.id_user = users.id
        LEFT JOIN buku ON penyewaan.id_buku = buku.id
        ORDER BY penyewaan.id DESC
    ");
}

function updateStatusPenyewaan($data)
{
    global $conn;

    $id = (int) $data['id'];
    $allowedStatus = ['pending', 'disewa', 'terlambat', 'dikembalikan'];
    $statusInput = strtolower((string) ($data['status'] ?? 'pending'));
    $statusInput = in_array($statusInput, $allowedStatus, true) ? $statusInput : 'pending';
    $status = mysqli_real_escape_string($conn, $statusInput === 'terlambat' ? 'disewa' : $statusInput);
    $tanggalDikembalikan = $statusInput === 'dikembalikan' ? date('Y-m-d') : null;

    $tanggalKembaliSql = "";

    if($statusInput === 'terlambat'){
        $dataPenyewaan = query("SELECT tanggal_kembali FROM penyewaan WHERE id = $id LIMIT 1");
        $tanggalKembali = $dataPenyewaan[0]['tanggal_kembali'] ?? null;

        if(hitungHariTerlambat($tanggalKembali) === 0){
            $tanggalTelat = date('Y-m-d', strtotime('-1 day'));
            $tanggalKembaliSql = ", tanggal_kembali = '$tanggalTelat'";
        }
    }

    if($statusInput === 'disewa'){
        $dataPenyewaan = query("SELECT tanggal_kembali FROM penyewaan WHERE id = $id LIMIT 1");
        $tanggalKembali = $dataPenyewaan[0]['tanggal_kembali'] ?? null;

        if(hitungHariTerlambat($tanggalKembali) > 0){
            $tanggalBaru = date('Y-m-d', strtotime('+7 days'));
            $tanggalKembaliSql = ", tanggal_kembali = '$tanggalBaru'";
        }
    }

    if($tanggalDikembalikan){
        mysqli_query($conn, "
            UPDATE penyewaan SET
                status = '$status',
                tanggal_dikembalikan = '$tanggalDikembalikan'
            WHERE id = $id
        ");
    }else{
        mysqli_query($conn, "
            UPDATE penyewaan SET
                status = '$status',
                tanggal_dikembalikan = NULL
                $tanggalKembaliSql
            WHERE id = $id
        ");
    }

    return mysqli_affected_rows($conn) >= 0;
}
?>
