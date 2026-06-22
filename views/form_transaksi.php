<?php defined('SECURE_ACCESS') or die(header("Location: index.php?action=login")); ?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rental AM - Transaksi Kasir</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <?php require_once 'views/layout/sidebar.php'; ?>

    <div class="main-content">
        <h1 class="page-title">TRANSAKSI RENTAL KENDARAAN</h1>

        <?php if (!empty($error)): ?>
            <div class="alert alert-danger">
                <i class="fa-solid fa-circle-exclamation"></i> <?php echo htmlspecialchars($error); ?>
            </div>
        <?php endif; ?>

        <?php if ($is_vehicles_empty): ?>
            <div class="alert alert-danger">
                <i class="fa-solid fa-ban"></i> <strong>Peringatan Keamanan!</strong> Transaksi ditolak atau diblokir secara otomatis karena data master kendaraan masih kosong. Silakan isi master kendaraan terlebih dahulu sebelum membuat transaksi.
            </div>
        <?php endif; ?>

        <div style="display: grid; grid-template-columns: <?php echo $is_vehicles_empty ? '1fr' : '1fr 2fr'; ?>; gap: 30px; align-items: start;">
            <?php if (!$is_vehicles_empty): ?>
                <div class="card">
                    <h2 class="card-title">Mulai Sewa Baru</h2>
                    <form action="index.php?action=transaksi_create" method="POST">
                        <div class="form-group">
                            <label>Pilih Pelanggan</label>
                            <select name="id_pelanggan" class="form-control" required>
                                <option value="">-- Pilih Pelanggan --</option>
                                <?php foreach ($customers as $c): ?>
                                    <option value="<?php echo $c['id_pelanggan']; ?>"><?php echo htmlspecialchars($c['nama_pelanggan']) . " (" . htmlspecialchars($c['nik']) . ")"; ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Pilih Kendaraan</label>
                            <select name="id_kendaraan" class="form-control" required>
                                <option value="">-- Pilih Kendaraan --</option>
                                <?php foreach ($vehicles as $v): ?>
                                    <?php if ($v['status'] === 'Tersedia'): ?>
                                        <option value="<?php echo $v['id_kendaraan']; ?>"><?php echo htmlspecialchars($v['nama_kendaraan']) . " - " . htmlspecialchars($v['no_plat']) . " (" . htmlspecialchars($v['jenis_kendaraan']) . ")"; ?></option>
                                    <?php endif; ?>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Tanggal Sewa Kontrak</label>
                            <input type="date" name="tgl_sewa" class="form-control" value="<?php echo date('Y-m-d'); ?>" required>
                        </div>
                        <div class="form-group">
                            <label>Tanggal Kembali Kontrak</label>
                            <input type="date" name="tgl_kembali" class="form-control" value="<?php echo date('Y-m-d', strtotime('+1 day')); ?>" required>
                        </div>
                        <button type="submit" class="btn btn-primary" style="width: 100%; justify-content: center; margin-top: 10px;">
                            <i class="fa-solid fa-receipt"></i> Daftarkan Transaksi
                        </button>
                    </form>
                </div>
            <?php endif; ?>

            <div class="card">
                <div class="search-sort-bar">
                    <form action="index.php" method="GET" class="search-box">
                        <input type="hidden" name="action" value="form_transaksi">
                        <input type="text" name="search" class="form-control" placeholder="Cari penyewa, kendaraan..." value="<?php echo htmlspecialchars($search ?? ''); ?>">
                        <button type="submit" class="btn btn-primary"><i class="fa-solid fa-magnifying-glass"></i></button>
                    </form>
                </div>

                <div class="table-responsive">
                    <table>
                        <thead>
                            <tr>
                                <th>Pelanggan</th>
                                <th>Kendaraan</th>
                                <th>Masa Sewa</th>
                                <th>Durasi</th>
                                <th>Total Biaya</th>
                                <th>Status</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($transactions)): ?>
                                <tr>
                                    <td colspan="7" style="text-align: center; padding: 30px;">Belum ada data transaksi sewa.</td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($transactions as $t): ?>
                                    <tr>
                                        <td><strong><?php echo htmlspecialchars($t['nama_pelanggan']); ?></strong></td>
                                        <td>
                                            <?php echo htmlspecialchars($t['nama_kendaraan']); ?><br>
                                            <span style="font-size:11px; opacity:0.7;"><?php echo htmlspecialchars($t['no_plat']); ?> (<?php echo htmlspecialchars($t['jenis_kendaraan']); ?>)</span>
                                        </td>
                                        <td>
                                            <span style="font-size:12px;"><i class="fa-regular fa-calendar"></i> <?php echo date('d M Y', strtotime($t['tgl_sewa'])); ?> s/d <?php echo date('d M Y', strtotime($t['tgl_kembali'])); ?></span>
                                            <?php if (!empty($t['tgl_pengembalian_riil'])): ?>
                                                <br><span style="font-size:11px; color:#28A745;">Riil: <?php echo date('d M Y', strtotime($t['tgl_pengembalian_riil'])); ?></span>
                                            <?php endif; ?>
                                        </td>
                                        <td><?php echo $t['lama_sewa']; ?> Hari</td>
                                        <td>
                                            <strong>Rp <?php echo number_format($t['total_biaya'], 0, ',', '.'); ?></strong>
                                            <?php if ($t['denda'] > 0): ?>
                                                <br><span style="font-size:11px; color:#FF5A5A;">+ Denda Rp<?php echo number_format($t['denda'], 0, ',', '.'); ?></span>
                                            <?php endif; ?>
                                            <?php if ($t['diskon'] > 0): ?>
                                                <br><span style="font-size:11px; color:#28A745;">- Potongan Rp<?php echo number_format($t['diskon'], 0, ',', '.'); ?></span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?php if ($t['status_transaksi'] === 'Berjalan'): ?>
                                                <span class="badge badge-warning"><i class="fa-solid fa-spinner"></i> Berjalan</span>
                                            <?php else: ?>
                                                <span class="badge badge-success"><i class="fa-solid fa-square-check"></i> Selesai</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?php if ($t['status_transaksi'] === 'Berjalan'): ?>
                                                <button class="btn btn-secondary" style="padding: 6px 12px; font-size:12px;" onclick="openReturnModal(<?php echo htmlspecialchars(json_encode($t)); ?>)">
                                                    <i class="fa-solid fa-reply"></i> Kembalikan
                                                </button>
                                            <?php endif; ?>
                                            <a href="index.php?action=transaksi_delete&id=<?php echo $t['id_transaksi']; ?>" class="btn btn-danger" style="padding: 6px 12px; font-size:12px;" onclick="return confirm('Hapus transaksi ini?')">
                                                <i class="fa-solid fa-trash"></i>
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="modal" id="returnModal">
        <div class="modal-content">
            <div class="modal-header">
                <h4>Proses Pengembalian Kendaraan</h4>
                <button class="close-btn" onclick="closeReturnModal()">&times;</button>
            </div>
            <form action="index.php?action=transaksi_update" method="POST">
                <input type="hidden" name="id_transaksi" id="return_id">
                <input type="hidden" name="status_transaksi" value="Selesai">
                <div class="form-group">
                    <label>Penyewa</label>
                    <input type="text" id="return_pelanggan" class="form-control" readonly>
                </div>
                <div class="form-group">
                    <label>Kendaraan</label>
                    <input type="text" id="return_kendaraan" class="form-control" readonly>
                </div>
                <div class="form-group">
                    <label>Tanggal Kembali Kontrak</label>
                    <input type="text" id="return_kontrak" class="form-control" readonly>
                </div>
                <div class="form-group">
                    <label>Tanggal Pengembalian Riil</label>
                    <input type="date" name="tgl_pengembalian_riil" id="return_riil" class="form-control" value="<?php echo date('Y-m-d'); ?>" required>
                </div>
                <div style="display:flex; justify-content:flex-end; gap:10px; margin-top:20px;">
                    <button type="button" class="btn btn-secondary" onclick="closeReturnModal()">Batal</button>
                    <button type="submit" class="btn btn-primary">Selesaikan Transaksi</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function openReturnModal(data) {
            document.getElementById('return_id').value = data.id_transaksi;
            document.getElementById('return_pelanggan').value = data.nama_pelanggan;
            document.getElementById('return_kendaraan').value = data.nama_kendaraan + ' (' + data.no_plat + ')';
            document.getElementById('return_kontrak').value = data.tgl_kembali;
            document.getElementById('returnModal').classList.add('active');
        }
        function closeReturnModal() {
            document.getElementById('returnModal').classList.remove('active');
        }
        window.onclick = function(event) {
            let returnModal = document.getElementById('returnModal');
            if (event.target === returnModal) {
                closeReturnModal();
            }
        }
    </script>
</body>
</html>
