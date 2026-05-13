<?php
session_start();

if (isset($_SESSION['user_id'])) {
    header('Location: dashboard.php');
    exit;
}

require 'koneksi.php';

$error   = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama     = trim($_POST['nama']     ?? '');
    $email    = trim($_POST['email']    ?? '');
    $password = $_POST['password']      ?? '';
    $konfirm  = $_POST['konfirmasi']    ?? '';

    if (empty($nama) || empty($email) || empty($password) || empty($konfirm)) {
        $error = 'Semua field wajib diisi.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Format email tidak valid.';
    } elseif (strlen($password) < 6) {
        $error = 'Password minimal 6 karakter.';
    } elseif ($password !== $konfirm) {
        $error = 'Password dan konfirmasi tidak cocok.';
    } else {
        $email_safe = mysqli_real_escape_string($koneksi, $email);

        $cek = mysqli_query($koneksi, "SELECT id FROM users WHERE email = '$email_safe' LIMIT 1");
        if ($cek && mysqli_num_rows($cek) > 0) {
            $error = 'Email sudah terdaftar. Gunakan email lain.';
        } else {
            $nama_safe  = mysqli_real_escape_string($koneksi, $nama);
            $hash       = password_hash($password, PASSWORD_DEFAULT);
            $sql = "INSERT INTO users (nama, email, password) VALUES ('$nama_safe', '$email_safe', '$hash')";
            if (mysqli_query($koneksi, $sql)) {
                $success = 'Registrasi berhasil! Silakan login.';
            } else {
                $error = 'Terjadi kesalahan. Coba lagi.';
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrasi — SmartParking Report</title>
    <link rel="stylesheet" href="assets/style.css">
</head>
<body>
<div class="auth-wrapper">
    <div class="auth-card">
        <div class="auth-logo">
           
            <h1>Daftar Akun</h1>
            <p>SmartParking Report  — Kota Bandung</p>
        </div>

        <?php if ($error): ?>
            <div class="alert alert-danger">⚠️ <?= htmlspecialchars($error) ?></div>
        <?php endif; ?>
        <?php if ($success): ?>
            <div class="alert alert-success">✅ <?= htmlspecialchars($success) ?></div>
        <?php endif; ?>

        <form method="POST" action="register.php" novalidate>
            <div class="form-group">
                <label for="nama"> Nama Lengkap</label>
                <input type="text" id="nama" name="nama" class="form-control"
                       placeholder="Nama lengkap "
                       value="<?= htmlspecialchars($_POST['nama'] ?? '') ?>" required>
            </div>

            <div class="form-group">
                <label for="email"> Email</label>
                <input type="email" id="email" name="email" class="form-control"
                       placeholder="contoh@email.com"
                       value="<?= htmlspecialchars($_POST['email'] ?? '') ?>" required>
            </div>

            <div class="form-group">
                <label for="password"> Password</label>
                <input type="password" id="password" name="password" class="form-control"
                       placeholder="Minimal 6 karakter" required>
            </div>

            <div class="form-group">
                <label for="konfirmasi"> Konfirmasi Password</label>
                <input type="password" id="konfirmasi" name="konfirmasi" class="form-control"
                       placeholder="Konfirmasi ulang password" required>
            </div>

            <button type="submit" class="btn btn-primary btn-block">
                📝 Daftar Sekarang
            </button>
        </form>

        <div class="auth-link">
            Sudah punya akun? <a href="login.php">Login di sini</a>
        </div>
    </div>
</div>
</body>
</html>
