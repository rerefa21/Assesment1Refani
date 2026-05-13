<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

require 'koneksi.php';
$user_id = $_SESSION['user_id'];

$error   = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
   
    $lokasi           = trim($_POST['lokasi']            ?? '');
    $waktu_laporan    = trim($_POST['waktu_laporan']     ?? '');
    $jumlah_kendaraan = trim($_POST['jumlah_kendaraan']  ?? '');
    $deskripsi        = trim($_POST['deskripsi']         ?? '');

    if (empty($lokasi) || empty($waktu_laporan) || empty($jumlah_kendaraan) || empty($deskripsi)) {
        $error = 'Semua field wajib diisi.';
    } elseif (!is_numeric($jumlah_kendaraan) || (int)$jumlah_kendaraan < 1) {
        $error = 'Jumlah kendaraan harus berupa angka positif.';
    } else {
        $jumlah = (int)$jumlah_kendaraan;

        if ($jumlah >= 1 && $jumlah <= 5) {
            $status = 'ringan';
        } elseif ($jumlah >= 6 && $jumlah <= 15) {
            $status = 'sedang';
        } else {
            $status = 'berat';
        }

        $foto_bukti = '';
        if (isset($_FILES['foto_bukti']) && $_FILES['foto_bukti']['error'] === UPLOAD_ERR_OK) {
            $file     = $_FILES['foto_bukti'];
            $ext      = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
            $allowed  = ['jpg', 'jpeg', 'png'];

            if (!in_array($ext, $allowed)) {
                $error = 'Format file hanya jpg, jpeg, atau png.';
            } elseif ($file['size'] > 5 * 1024 * 1024) {
                $error = 'Ukuran file maksimal 5 MB.';
            } else {
                $nama_file = 'parkir_' . time() . '_' . uniqid() . '.' . $ext;
                $tujuan    = 'uploads/' . $nama_file;
                if (move_uploaded_file($file['tmp_name'], $tujuan)) {
                    $foto_bukti = $nama_file;
                } else {
                    $error = 'Gagal mengupload file. Pastikan folder uploads/ ada dan writable.';
                }
            }
        }

        if (empty($error)) {
            $lokasi_s       = mysqli_real_escape_string($koneksi, $lokasi);
            $waktu_s        = mysqli_real_escape_string($koneksi, $waktu_laporan);
            $deskripsi_s    = mysqli_real_escape_string($koneksi, $deskripsi);
            $foto_s         = mysqli_real_escape_string($koneksi, $foto_bukti);

            $sql = "INSERT INTO laporan (user_id, lokasi, waktu_laporan, jumlah_kendaraan, status_pelanggaran, deskripsi, foto_bukti)
                    VALUES ($user_id, '$lokasi_s', '$waktu_s', $jumlah, '$status', '$deskripsi_s', '$foto_s')";

            if (mysqli_query($koneksi, $sql)) {
                $_SESSION['success'] = 'Laporan berhasil ditambahkan!';
                header('Location: laporan.php');
                exit;
            } else {
                $error = 'Gagal menyimpan laporan: ' . mysqli_error($koneksi);
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
    <title>Tambah Laporan — SmartParking Report</title>
    <link rel="stylesheet" href="assets/style.css">
</head>
<body>

<?php require 'navbar.php'; ?>

<div class="main-content">
    <div class="page-header">
        <h1 class="page-title">➕ Tambah <span>Laporan Baru</span></h1>
        <a href="laporan.php" class="btn btn-secondary">← Kembali</a>
    </div>

    <?php if ($error): ?>
        <div class="alert alert-danger">⚠️ <?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <div class="form-section">
        <h2>📝 Form Laporan Parkir Liar</h2>

        <form method="POST" action="tambah_laporan.php" enctype="multipart/form-data" novalidate>

            <div class="form-row">
                <div class="form-group">
                    <label for="lokasi">📍 Lokasi Kejadian</label>
                    <input type="text" id="lokasi" name="lokasi" class="form-control"
                           placeholder="Contoh: Pasar Baleendah, RSUD, dll."
                           value="<?= htmlspecialchars($_POST['lokasi'] ?? '') ?>" required>
                </div>
                <div class="form-group">
                    <label for="waktu_laporan">🕐 Waktu Laporan</label>
                    <input type="datetime-local" id="waktu_laporan" name="waktu_laporan"
                           class="form-control"
                           value="<?= htmlspecialchars($_POST['waktu_laporan'] ?? date('Y-m-d\TH:i')) ?>" required>
                </div>
            </div>

            <div class="form-group">
                <label for="jumlah_kendaraan">🚗 Jumlah Kendaraan Parkir Liar</label>
                <input type="number" id="jumlah_kendaraan" name="jumlah_kendaraan"
                       class="form-control" placeholder="Masukkan angka jumlah kendaraan"
                       min="1" value="<?= htmlspecialchars($_POST['jumlah_kendaraan'] ?? '') ?>" required>
                <p class="file-info">
                    1–5 = Ringan &nbsp;|&nbsp; 6–15 = Sedang &nbsp;|&nbsp; &gt;15 = Berat
                    (status otomatis ditentukan sistem)
                </p>
            </div>

            <div class="form-group">
                <label for="deskripsi">📝 Deskripsi Kondisi Lapangan</label>
                <textarea id="deskripsi" name="deskripsi" class="form-control"
                          placeholder="Jelaskan kondisi di lapangan secara detail..." required><?= htmlspecialchars($_POST['deskripsi'] ?? '') ?></textarea>
            </div>

            <div class="form-group">
                <label for="foto_bukti">📸 Foto Bukti (Opsional)</label>
                <input type="file" id="foto_bukti" name="foto_bukti" class="form-control"
                       accept=".jpg,.jpeg,.png">
                <p class="file-info">Format yang diizinkan: JPG, JPEG, PNG. Maks 5 MB.</p>
            </div>

            <div style="display:flex;gap:1rem;margin-top:.5rem;">
                <button type="submit" class="btn btn-primary">💾 Simpan Laporan</button>
                <a href="laporan.php" class="btn btn-secondary">✖ Batal</a>
            </div>
        </form>
    </div>
</div>

<?php require 'footer.php'; ?>
</body>
</html>