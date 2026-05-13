<?php
session_start();

    if (!isset($_SESSION['user_id'])) {
        header('Location: login.php');
        exit;
    }

    require 'koneksi.php';
    $user_id = $_SESSION['user_id'];

    $sql    = "SELECT * FROM laporan WHERE user_id = $user_id ORDER BY waktu_laporan DESC";
    $result = mysqli_query($koneksi, $sql);

    $success = $_SESSION['success'] ?? '';
    if ($success) unset($_SESSION['success']);
    ?>
    <!DOCTYPE html>
    <html lang="id">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Daftar Laporan — SmartParking Report</title>
        <link rel="stylesheet" href="assets/style.css">
    </head>
    <body>

    <?php require 'navbar.php'; ?>

    <div class="main-content">
        <div class="page-header">
            <h1 class="page-title">📋 Daftar <span>Laporan Parkir Liar</span></h1>
            <a href="tambah_laporan.php" class="btn btn-primary">➕ Tambah Laporan</a>
        </div>

        <?php if ($success): ?>
            <div class="alert alert-success">✅ <?= htmlspecialchars($success) ?></div>
        <?php endif; ?>

        <div class="card">
            <div class="card-header">
                <h2>📄 Semua Laporan Anda</h2>
                <span style="color:#64748b;font-size:.85rem;">Total: <?= mysqli_num_rows($result) ?> laporan</span>
            </div>
            <div class="table-responsive">
                <?php if (mysqli_num_rows($result) > 0): ?>
                <table>
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Lokasi</th>
                            <th>Waktu Laporan</th>
                            <th>Kendaraan</th>
                            <th>Status</th>
                            <th>Deskripsi</th>
                            <th>Foto Bukti</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $no = 1; while ($row = mysqli_fetch_assoc($result)): ?>
                        <tr>
                            <td><?= $no++ ?></td>
                            <td><strong><?= htmlspecialchars($row['lokasi']) ?></strong></td>
                            <td><?= date('d/m/Y H:i', strtotime($row['waktu_laporan'])) ?></td>
                            <td><?= $row['jumlah_kendaraan'] ?> unit</td>
                            <td>
                                <span class="badge badge-<?= $row['status_pelanggaran'] ?>">
                                    <?= ucfirst($row['status_pelanggaran']) ?>
                                </span>
                            </td>
                            <td style="max-width:200px;">
                                <?= htmlspecialchars(substr($row['deskripsi'], 0, 60)) ?>
                                <?= strlen($row['deskripsi']) > 60 ? '...' : '' ?>
                            </td>
                            <td>
                                <?php if ($row['foto_bukti']): ?>
                                    <img src="uploads/<?= htmlspecialchars($row['foto_bukti']) ?>"
                                        alt="foto" class="foto-preview"
                                        onclick="window.open('uploads/<?= htmlspecialchars($row['foto_bukti']) ?>','_blank')"
                                        title="Klik untuk lihat full">
                                <?php else: ?>
                                    <span class="no-foto">Tidak ada</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <a href="edit_laporan.php?id=<?= $row['id'] ?>" class="btn btn-sm btn-warning">✏️ Edit</a>
                                <a href="hapus_laporan.php?id=<?= $row['id'] ?>"
                                class="btn btn-sm btn-danger"
                                onclick="return confirm('Yakin hapus laporan ini? File foto juga akan dihapus.')">
                                🗑️ Hapus
                                </a>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
                <?php else: ?>
                <div class="empty-state">
                    <div class="empty-icon">📭</div>
                    <p>Belum ada laporan. <a href="tambah_laporan.php">Tambah laporan sekarang</a>.</p>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <?php require 'footer.php'; ?>
    </body>
</html>