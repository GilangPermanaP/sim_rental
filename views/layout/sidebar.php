<?php
defined('SECURE_ACCESS') or die('Direct access not permitted');
$current_action = $_GET['action'] ?? 'dashboard';
$user_role = $_SESSION['role'] ?? 'Operator';
$sidebar_class = ($current_action === 'cetak_laporan') ? 'sidebar no-print' : 'sidebar';
?>
<div class="<?php echo $sidebar_class; ?>">
    <div class="sidebar-brand">
        <img src="assets/images/logo.svg" alt="Rental AM Logo" class="brand-logo">
    </div>
    <ul class="sidebar-menu">
        <li class="<?php echo ($current_action === 'dashboard') ? 'active' : ''; ?>">
            <a href="index.php?action=dashboard">
                <i class="fa-solid fa-chart-line"></i> Dashboard
            </a>
        </li>
        <?php if ($user_role === 'Admin'): ?>
        <li class="<?php echo (strpos($current_action, 'kendaraan') !== false || $current_action === 'master_kendaraan') ? 'active' : ''; ?>">
            <a href="index.php?action=master_kendaraan">
                <i class="fa-solid fa-car"></i> Master Kendaraan
            </a>
        </li>
        <?php endif; ?>
        <li class="<?php echo (strpos($current_action, 'pelanggan') !== false || $current_action === 'master_pelanggan') ? 'active' : ''; ?>">
            <a href="index.php?action=master_pelanggan">
                <i class="fa-solid fa-users"></i> Master Pelanggan
            </a>
        </li>
        <?php if ($user_role === 'Admin'): ?>
        <li class="<?php echo (strpos($current_action, 'tarif_sewa') !== false || $current_action === 'master_tarif_sewa') ? 'active' : ''; ?>">
            <a href="index.php?action=master_tarif_sewa">
                <i class="fa-solid fa-tags"></i> Master Tarif Sewa
            </a>
        </li>
        <li class="<?php echo (strpos($current_action, 'user') !== false && $current_action !== 'logout') ? 'active' : ''; ?>">
            <a href="index.php?action=master_user">
                <i class="fa-solid fa-user-gear"></i> Master User
            </a>
        </li>
        <?php endif; ?>
        <li class="<?php echo (strpos($current_action, 'transaksi') !== false || $current_action === 'form_transaksi') ? 'active' : ''; ?>">
            <a href="index.php?action=form_transaksi">
                <i class="fa-solid fa-cash-register"></i> Transaksi Kasir
            </a>
        </li>
        <?php if ($user_role === 'Admin'): ?>
        <li class="<?php echo ($current_action === 'cetak_laporan') ? 'active' : ''; ?>">
            <a href="index.php?action=cetak_laporan">
                <i class="fa-solid fa-file-invoice-dollar"></i> Laporan Keuangan
            </a>
        </li>
        <?php endif; ?>
    </ul>
    <div class="sidebar-footer">
        <div class="sidebar-user">
            <div class="sidebar-user-avatar">
                <?php echo strtoupper(substr($_SESSION['user']['nama_lengkap'] ?? 'A', 0, 1)); ?>
            </div>
            <div class="sidebar-user-info">
                <h5><?php echo htmlspecialchars($_SESSION['user']['nama_lengkap'] ?? 'AM User'); ?></h5>
                <p><?php echo htmlspecialchars($user_role); ?></p>
            </div>
        </div>
        <a href="index.php?action=logout" class="sidebar-footer-btn">
            <i class="fa-solid fa-right-from-bracket"></i> Keluar Sistem
        </a>
    </div>
</div>
