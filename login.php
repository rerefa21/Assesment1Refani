<?php
session_start();

if (isset($_SESSION['user_id'])) {
    header('Location: dashboard.php');
    exit;
}

require 'koneksi.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email    = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if (empty($email) || empty($password)) {
        $error = 'Email dan password wajib diisi.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Format email tidak valid.';
    } elseif (strlen($password) < 6) {
        $error = 'Password minimal 6 karakter.';
    } else {
        $email_safe = mysqli_real_escape_string($koneksi, $email);
        $sql  = "SELECT * FROM users WHERE email = '$email_safe' LIMIT 1";
        $result = mysqli_query($koneksi, $sql);

        if ($result && mysqli_num_rows($result) === 1) {
            $user = mysqli_fetch_assoc($result);
            if (password_verify($password, $user['password'])) {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['nama']    = $user['nama'];
                $_SESSION['email']   = $user['email'];
                header('Location: dashboard.php');
                exit;
            } else {
                $error = 'Email atau password salah.';
            }
        } else {
            $error = 'Email atau password salah.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login — SmartParking Report</title>
    <link rel="stylesheet" href="assets/style.css">
</head>
<body>
<div class="auth-wrapper">
    <div class="auth-card">
        <div class="auth-logo">
            <h1>SmartParking Report</h1>
            <p>Sistem Pelaporan Parkir Liar — Kota Bandung</p>
        </div>

        <?php if ($error): ?>
            <div class="alert alert-danger">⚠️ <?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <form method="POST" action="login.php" novalidate>
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

            <button type="submit" class="btn btn-primary btn-block">
                🚀 Masuk
            </button>
        </form>

        <div class="auth-link">
            Belum punya akun? <a href="register.php">Daftar di sini</a>
        </div>
    </div>
</div>
</body>
</html>