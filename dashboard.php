<?php
session_start();

    if (!isset($_SESSION['user_id'])) {
        header('Location: login.php');
        exit;
    }

    require 'koneksi.php';

    $user_id = $_SESSION['user_id'];

    $total_q   = mysqli_query($koneksi, "SELECT COUNT(*) as total FROM laporan WHERE user_id = $user_id");
    $total     = mysqli_fetch_assoc($total_q)['total'];

    $ringan_q  = mysqli_query($koneksi, "SELECT COUNT(*) as jml FROM laporan WHERE user_id = $user_id AND status_pelanggaran = 'ringan'");
    $ringan    = mysqli_fetch_assoc($ringan_q)['jml'];

    $sedang_q  = mysqli_query($koneksi, "SELECT COUNT(*) as jml FROM laporan WHERE user_id = $user_id AND status_pelanggaran = 'sedang'");
    $sedang    = mysqli_fetch_assoc($sedang_q)['jml'];

    $berat_q   = mysqli_query($koneksi, "SELECT COUNT(*) as jml FROM laporan WHERE user_id = $user_id AND status_pelanggaran = 'berat'");
    $berat     = mysqli_fetch_assoc($berat_q)['jml'];

    $terbaru_q = mysqli_query($koneksi,
        "SELECT * FROM laporan WHERE user_id = $user_id ORDER BY waktu_laporan DESC LIMIT 5"
    );
    ?>
    <!DOCTYPE html>
    <html lang="id">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Dashboard — SmartParking Report</title>
        <link rel="stylesheet" href="assets/style.css">
    </head>
    <body>

    <?php require 'navbar.php'; ?>

    <div class="main-content">

        <div class="page-header">
            <div>
                <h1 class="page-title">🏠 Dashboard <span>Parkir </span></h1>
                <p style="color:#64748b;font-size:.9rem;">Selamat datang, <strong><?= htmlspecialchars($_SESSION['nama']) ?></strong></p>
            </div>
            <a href="tambah_laporan.php" class="btn btn-primary"> Tambah Laporan</a>
        </div>

        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-icon blue">📋</div>
                <div class="stat-info">
                    <h3><?= $total ?></h3>
                    <p>Total Laporan</p>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon green">🟢</div>
                <div class="stat-info">
                    <h3><?= $ringan ?></h3>
                    <p>Pelanggaran Ringan</p>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon yellow">🟡</div>
                <div class="stat-info">
                    <h3><?= $sedang ?></h3>
                    <p>Pelanggaran Sedang</p>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon red">🔴</div>
                <div class="stat-info">
                    <h3><?= $berat ?></h3>
                    <p>Pelanggaran Berat</p>
                </div>
            </div>
        </div>

        <div class="card latest-section">
            <div class="card-header">
                <h2>📌 Laporan Terbaru</h2>
                <a href="laporan.php" class="btn btn-sm btn-secondary">Lihat Semua</a>
            </div>
            <div class="table-responsive">
                <?php if (mysqli_num_rows($terbaru_q) > 0): ?>
                <table>
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Lokasi</th>
                            <th>Waktu</th>
                            <th>Kendaraan</th>
                            <th>Status</th>
                            <th>Deskripsi</th>
                            <th>Foto</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $no = 1; while ($row = mysqli_fetch_assoc($terbaru_q)): ?>
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
                            <td><?= htmlspecialchars(substr($row['deskripsi'], 0, 40)) ?>
                                <?= strlen($row['deskripsi']) > 40 ? '...' : '' ?>
                            </td>
                            <td>
                                <?php if ($row['foto_bukti']): ?>
                                    <img src="uploads/<?= htmlspecialchars($row['foto_bukti']) ?>"
                                        alt="foto" class="foto-preview"
                                        onclick="window.open('uploads/<?= htmlspecialchars($row['foto_bukti']) ?>','_blank')">
                                <?php else: ?>
                                    <span class="no-foto">—</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
                <?php else: ?>
                <div class="empty-state">
                    <div class="empty-icon">📭</div>
                    <p>Belum ada laporan. <a href="tambah_laporan.php">Tambah laporan pertama</a>.</p>
                </div>
                <?php endif; ?>
            </div>
        </div>

    </div>

    <?php require 'footer.php'; ?>
    </body>
</html>