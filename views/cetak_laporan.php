<?php defined('SECURE_ACCESS') or die(header("Location: index.php?action=login")); ?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rental AM - Laporan Keuangan</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <?php require_once 'views/layout/sidebar.php'; ?>

    <div class="main-content">
        <h1 class="page-title no-print">LAPORAN KEUANGAN & MASTER</h1>

        <div class="card no-print">
            <h2 class="card-title">Filter Jangka Tanggal Transaksi</h2>
            <form action="index.php" method="GET" style="display:flex; gap:20px; align-items:flex-end; flex-wrap:wrap;">
                <input type="hidden" name="action" value="cetak_laporan">
                <div class="form-group" style="margin-bottom:0; flex:1; min-width:200px;">
                    <label>Tanggal Mulai</label>
                    <input type="date" name="start_date" class="form-control" value="<?php echo htmlspecialchars($start_date); ?>">
                </div>
                <div class="form-group" style="margin-bottom:0; flex:1; min-width:200px;">
                    <label>Tanggal Selesai</label>
                    <input type="date" name="end_date" class="form-control" value="<?php echo htmlspecialchars($end_date); ?>">
                </div>
                <button type="submit" class="btn btn-secondary"><i class="fa-solid fa-filter"></i> Saring Data</button>
                <button type="button" class="btn btn-primary" onclick="window.print()"><i class="fa-solid fa-print"></i> Cetak Laporan</button>
            </form>
        </div>

        <div class="print-report-container">
            <div class="report-header">
                <h2>LAPORAN RENTAL AM</h2>
                <p style="font-weight:600; opacity:0.8; margin-top:5px;">Atas Nama: Rental AM</p>
                <p style="font-size:12px; margin-top:5px; opacity:0.6;">Dicetak pada: <?php echo date('d-m-Y H:i:s'); ?></p>
            </div>

            <div class="report-section">
                <h3 class="report-section-title">Modul 1: Laporan Master Data</h3>
                
                <h4 style="margin-top:15px; margin-bottom:5px; font-size:14px; text-transform:uppercase;">A. Data Kendaraan</h4>
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nama Kendaraan</th>
                            <th>No Plat</th>
                            <th>Kelas</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($master_data['kendaraan'] as $mk): ?>
                            <tr>
                                <td><?php echo $mk['id_kendaraan']; ?></td>
                                <td><?php echo htmlspecialchars($mk['nama_kendaraan']); ?></td>
                                <td><?php echo htmlspecialchars($mk['no_plat']); ?></td>
                                <td><?php echo htmlspecialchars($mk['jenis_kendaraan']); ?></td>
                                <td><?php echo htmlspecialchars($mk['status']); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>

                <h4 style="margin-top:25px; margin-bottom:5px; font-size:14px; text-transform:uppercase;">B. Data Pelanggan</h4>
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nama Pelanggan</th>
                            <th>No Telp</th>
                            <th>Alamat</th>
                            <th>NIK</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($master_data['pelanggan'])): ?>
                            <tr>
                                <td colspan="5" style="text-align:center;">Tidak ada data pelanggan.</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($master_data['pelanggan'] as $mp): ?>
                                <tr>
                                    <td><?php echo $mp['id_pelanggan']; ?></td>
                                    <td><?php echo htmlspecialchars($mp['nama_pelanggan']); ?></td>
                                    <td><?php echo htmlspecialchars($mp['no_telp']); ?></td>
                                    <td><?php echo htmlspecialchars($mp['alamat']); ?></td>
                                    <td><?php echo htmlspecialchars($mp['nik']); ?></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>

                <h4 style="margin-top:25px; margin-bottom:5px; font-size:14px; text-transform:uppercase;">C. Daftar Tarif Sewa</h4>
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Kelas Kendaraan</th>
                            <th>Tarif Per Hari</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($master_data['tarif'] as $mt): ?>
                            <tr>
                                <td><?php echo $mt['id_tarif']; ?></td>
                                <td><?php echo htmlspecialchars($mt['jenis_kendaraan']); ?></td>
                                <td>Rp <?php echo number_format($mt['tarif_per_hari'], 0, ',', '.'); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <div class="report-section">
                <h3 class="report-section-title">Modul 2: Laporan Semua Transaksi</h3>
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Pelanggan</th>
                            <th>Kendaraan</th>
                            <th>Tgl Sewa</th>
                            <th>Tgl Kembali</th>
                            <th>Riil Kembali</th>
                            <th>Lama</th>
                            <th>Biaya</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($semua_transaksi)): ?>
                            <tr>
                                <td colspan="9" style="text-align:center;">Belum ada data transaksi.</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($semua_transaksi as $st): ?>
                                <tr>
                                    <td><?php echo $st['id_transaksi']; ?></td>
                                    <td><?php echo htmlspecialchars($st['nama_pelanggan']); ?></td>
                                    <td><?php echo htmlspecialchars($st['nama_kendaraan']); ?></td>
                                    <td><?php echo date('d-m-Y', strtotime($st['tgl_sewa'])); ?></td>
                                    <td><?php echo date('d-m-Y', strtotime($st['tgl_kembali'])); ?></td>
                                    <td><?php echo !empty($st['tgl_pengembalian_riil']) ? date('d-m-Y', strtotime($st['tgl_pengembalian_riil'])) : '-'; ?></td>
                                    <td><?php echo $st['lama_sewa']; ?> Hari</td>
                                    <td>Rp <?php echo number_format($st['total_biaya'], 0, ',', '.'); ?></td>
                                    <td><?php echo htmlspecialchars($st['status_transaksi']); ?></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <div class="report-section">
                <h3 class="report-section-title">Modul 3: Laporan Filter Jangka Tanggal</h3>
                <p style="font-size:13px; font-weight:600; margin-bottom:10px;">Periode: <?php echo date('d M Y', strtotime($start_date)); ?> s/d <?php echo date('d M Y', strtotime($end_date)); ?></p>
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Pelanggan</th>
                            <th>Kendaraan</th>
                            <th>Tgl Sewa</th>
                            <th>Tgl Kembali</th>
                            <th>Lama</th>
                            <th>Total Biaya</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($transaksi_filter)): ?>
                            <tr>
                                <td colspan="7" style="text-align:center;">Tidak ada transaksi pada jangka tanggal ini.</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($transaksi_filter as $tf): ?>
                                <tr>
                                    <td><?php echo $tf['id_transaksi']; ?></td>
                                    <td><?php echo htmlspecialchars($tf['nama_pelanggan']); ?></td>
                                    <td><?php echo htmlspecialchars($tf['nama_kendaraan']); ?></td>
                                    <td><?php echo date('d-m-Y', strtotime($tf['tgl_sewa'])); ?></td>
                                    <td><?php echo date('d-m-Y', strtotime($tf['tgl_kembali'])); ?></td>
                                    <td><?php echo $tf['lama_sewa']; ?> Hari</td>
                                    <td>Rp <?php echo number_format($tf['total_biaya'], 0, ',', '.'); ?></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <div class="report-section">
                <h3 class="report-section-title">Modul 4: Total Pendapatan Kumulatif</h3>
                <div class="cumulative-box">
                    <h4>Total Pendapatan Terkumpul Dari Keseluruhan Sewa:</h4>
                    <p>Rp <?php echo number_format($total_pendapatan, 0, ',', '.'); ?></p>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
