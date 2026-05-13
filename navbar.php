<?php
if (session_status() === PHP_SESSION_NONE) session_start();
?>
<nav class="navbar">
    <a href="dashboard.php" class="navbar-brand">
        SmartParking Report
    </a>

    <div class="navbar-menu">
        <a href="dashboard.php" <?= basename($_SERVER['PHP_SELF']) === 'dashboard.php' ? 'class="active"' : '' ?>>
            🏠 Dashboard
        </a>
        <a href="laporan.php" <?= in_array(basename($_SERVER['PHP_SELF']), ['laporan.php','tambah_laporan.php','edit_laporan.php']) ? 'class="active"' : '' ?>>
            📋 Laporan
        </a>
        <a href="tambah_laporan.php" <?= basename($_SERVER['PHP_SELF']) === 'tambah_laporan.php' ? 'class="active"' : '' ?>>
            ➕ Tambah
        </a>
    </div>

    <div class="navbar-user">      
        <a href="logout.php" class="btn btn-sm btn-logout">🚪 Logout</a>
    </div>
</nav>