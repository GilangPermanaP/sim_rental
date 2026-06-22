<?php defined('SECURE_ACCESS') or die(header("Location: index.php?action=login")); ?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rental AM - Dashboard</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <?php require_once 'views/layout/sidebar.php'; ?>

    <div class="main-content">
        <h1 class="page-title">DASHBOARD UTAMA</h1>
        
        <?php if (!empty($_SESSION['error_message'])): ?>
            <div class="alert alert-danger" style="margin-bottom: 20px;">
                <i class="fa-solid fa-triangle-exclamation"></i> <?php echo htmlspecialchars($_SESSION['error_message']); unset($_SESSION['error_message']); ?>
            </div>
        <?php endif; ?>

        <div class="dashboard-stats">
            <div class="stat-card">
                <div class="stat-icon">
                    <i class="fa-solid fa-users"></i>
                </div>
                <div class="stat-info">
                    <h3><?php echo number_format($stats['pelanggan']); ?></h3>
                    <p>Total Pelanggan</p>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon">
                    <i class="fa-solid fa-car-side"></i>
                </div>
                <div class="stat-info">
                    <h3><?php echo number_format($stats['kendaraan']); ?></h3>
                    <p>Total Kendaraan</p>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon">
                    <i class="fa-solid fa-receipt"></i>
                </div>
                <div class="stat-info">
                    <h3><?php echo number_format($stats['transaksi']); ?></h3>
                    <p>Total Transaksi</p>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon">
                    <i class="fa-solid fa-money-bill-wave"></i>
                </div>
                <div class="stat-info">
                    <h3>Rp <?php echo number_format($stats['revenue'], 0, ',', '.'); ?></h3>
                    <p>Total Pendapatan</p>
                </div>
            </div>
        </div>

        <div class="card">
            <h2 class="card-title">Selamat Datang di Rental AM</h2>
            <p>Sistem ini dirancang untuk memudahkan manajemen penyewaan kendaraan, pemantauan transaksi kasir, serta pencetakan laporan berkala secara cepat dan akurat.</p>
        </div>
    </div>
</body>
</html>
